<?php
/**
 * File was created 06.10.2015 06:25
 */

namespace PeekAndPoke\Component\Slumber\Core\LookUp;

use Doctrine\Common\Cache\Cache;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class CachedEntityConfigLookUp extends DelegatingEntityConfigReader
{
    /** @var Cache */
    private $cache;
    /** @var string */
    private $prefix;
    /** @var bool */
    private $debug;

    /**
     * We have another level of caching here
     *
     * @var array
     */
    private $known = [];

    /**
     * @param EntityConfigReader $delegate
     * @param Cache              $cache
     * @param string             $prefix
     * @param bool               $debug
     */
    public function __construct(EntityConfigReader $delegate, Cache $cache, string $prefix = '[Slumber]@', bool $debug = false)
    {
        parent::__construct($delegate);

        $this->cache  = $cache;
        $this->prefix = $prefix;
        $this->debug  = $debug;
    }

    /**
     * @param \ReflectionClass $cls
     *
     * @return PropertyMarkedForSlumber[]
     */
    public function getEntityConfig(\ReflectionClass $cls)
    {
        $cacheKey = $this->prefix . $cls->name;

        return $this->loadOrCreate(
            $cacheKey,
            function () use ($cacheKey, $cls) {
                return $this->fetchFromCache($cacheKey, $cls);
            },
            function () use ($cls) {
                return $this->delegate->getEntityConfig($cls);
            },
            $cls
        );
    }

    /**
     * @param string           $cacheKey
     * @param \Closure         $fetcher
     * @param \Closure         $creator
     * @param \ReflectionClass $class
     *
     * @return mixed
     */
    private function loadOrCreate($cacheKey, \Closure $fetcher, \Closure $creator, \ReflectionClass $class)
    {
        if (isset($this->known[$cacheKey])) {
            return $this->known[$cacheKey];
        }

        /** @var EntityConfig $cacheData */
        $cacheData = $fetcher();
        if (false === $cacheData) {
            $cacheData = $creator();
            $this->saveToCache($cacheKey, $cacheData, $class);
        }

        $cacheData->warmUp();

        return $this->known[$cacheKey] = $cacheData;
    }

    /**
     * Fetches a value from the cache.
     *
     * @param string           $rawCacheKey The cache key.
     * @param \ReflectionClass $class       The related class.
     *
     * @return mixed The cached value or false when the value is not in cache.
     */
    private function fetchFromCache($rawCacheKey, \ReflectionClass $class)
    {
        $cacheKey = $this->prefix . $rawCacheKey;

        if (($data = $this->cache->fetch($cacheKey)) !== false) {
            /** @noinspection NestedPositiveIfStatementsInspection */
            if (! $this->debug || $this->isCacheFresh($cacheKey, $class)) {
                return $data;
            }
        }

        return false;
    }

    /**
     * Saves a value to the cache.
     *
     * @param string           $rawCacheKey The cache key.
     * @param mixed            $value       The value.
     * @param \ReflectionClass $class
     */
    private function saveToCache($rawCacheKey, $value, \ReflectionClass $class)
    {
        $cacheKey = $this->prefix . $rawCacheKey;
        $this->cache->save($cacheKey, $value);

        // in debug mode record the creation time of the entry
        if ($this->debug && false !== $filename = $class->getFileName()) {

            $this->cache->save('[C]' . $cacheKey, filemtime($filename));
        }
    }

    /**
     * Checks if the cache is fresh.
     *
     * @param string           $cacheKey
     * @param \ReflectionClass $class
     *
     * @return boolean
     */
    private function isCacheFresh($cacheKey, \ReflectionClass $class)
    {
        if (false === $filename = $class->getFileName()) {
            return true;
        }

//        echo $cacheKey . " " . $this->cache->fetch('[C]'.$cacheKey) . " " . filemtime($filename) . " " . ($this->cache->fetch('[C]'.$cacheKey) >= filemtime($filename)) . "\n";

        // When we have the creation time (debug mode) and it is less than the current file time, the cache is also not
        // fresh
        return $this->cache->fetch('[C]' . $cacheKey) >= filemtime($filename);
    }
}
