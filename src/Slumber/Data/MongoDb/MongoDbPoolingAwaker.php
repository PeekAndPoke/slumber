<?php
/**
 * File was created 07.10.2015 06:35
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use PeekAndPoke\Component\Slumber\Data\EntityPool;
use PeekAndPoke\Component\Slumber\Data\Storage;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MongoDbPoolingAwaker implements MongoDbAwaker
{
    /** @var MongoDbAwaker */
    private $delegate;
    /** @var EntityPool */
    private $pool;
    /** @var MongoDbEntityConfigReader */
    private $lookUp;

    /**
     * @param MongoDbAwaker             $delegate
     * @param EntityPool                $pool
     * @param MongoDbEntityConfigReader $lookUp
     */
    public function __construct(MongoDbAwaker $delegate, EntityPool $pool, MongoDbEntityConfigReader $lookUp)
    {
        $this->delegate = $delegate;
        $this->pool     = $pool;
        $this->lookUp   = $lookUp;
    }

    /**
     * @return Storage
     */
    public function getStorage()
    {
        return $this->delegate->getStorage();
    }

    /**
     * @param mixed            $data
     * @param \ReflectionClass $cls
     *
     * @return mixed|null
     */
    public function awake($data, \ReflectionClass $cls)
    {
        $awoken = $this->delegate->awake($data, $cls);

        if ($awoken === null) {
            return null;
        }

        $awokenClass = new \ReflectionClass($awoken);

        // We need to get the idMarker from the awoken class
        $idAccess  = $this->lookUp->getEntityConfig($awokenClass)->getIdAccess();
        $primaryId = $idAccess->get($awoken);

        // Can we find this entity in the pool ?
        if ($primaryId !== null && $this->pool->has($cls, EntityPool::PRIMARY_ID, $primaryId)) {
            return $this->pool->get($cls, EntityPool::PRIMARY_ID, $primaryId);
        }

        // otherwise we set it on the pool
        $this->pool->set($cls, EntityPool::PRIMARY_ID, $primaryId, $awoken);

        return $awoken;
    }
}
