<?php
/**
 * File was created 11.10.2015 13:22
 */

namespace PeekAndPoke\Component\Slumber\Data;

use PeekAndPoke\Component\Psi\Psi;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class EntityPoolImpl implements EntityPool
{
    /** @var array */
    private $pool = [];
    /** @var int */
    private $numHits = 0;
    /** @var int */
    private $numMisses = 0;

    /**
     * @return EntityPoolImpl
     */
    public static function getInstance()
    {
        static $instance;

        return $instance ?: $instance = new static;
    }

    /**
     * Singleton constructor
     */
    protected function __construct() { }

    /**
     * @return array
     */
    public function all()
    {
        return $this->pool;
    }

    /**
     * @return EntityPoolStats
     */
    public function stats()
    {
        return new EntityPoolStats(\count($this->pool), $this->numHits, $this->numMisses);
    }

    ////  GET SET CHECK  ///////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param \ReflectionClass $cls
     * @param string           $idKey
     * @param mixed            $idValue
     *
     * @return bool
     */
    public function has(\ReflectionClass $cls, $idKey, $idValue)
    {
        $isset = isset($this->pool[$cls->name . '@' . $idKey . '@' . $idValue]);

        if ($isset) {
            ++$this->numHits;
        } else {
            ++$this->numMisses;
        }

        return $isset;
    }

    /**
     * @param \ReflectionClass $cls
     * @param string           $idKey
     * @param mixed            $idValue
     *
     * @return mixed
     */
    public function get(\ReflectionClass $cls, $idKey, $idValue)
    {
        return $this->pool[$cls->name . '@' . $idKey . '@' . $idValue];
    }

    /**
     * @param \ReflectionClass $cls
     * @param string           $idKey
     * @param mixed            $idValue
     * @param mixed            $value
     */
    public function set(\ReflectionClass $cls, $idKey, $idValue, $value)
    {
        $this->pool[$cls->name . '@' . $idKey . '@' . $idValue] = $value;
    }

    ////  REMOVING ITEMS  //////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Remove all entities from the pool
     */
    public function clear()
    {
        $this->pool = [];
    }

    /**
     * @param \ReflectionClass $cls
     * @param string           $idKey
     * @param string           $idValue
     */
    public function remove(\ReflectionClass $cls, $idKey, $idValue)
    {
        unset ($this->pool[$cls->name . '@' . $idKey . '@' . $idValue]);
    }

    /**
     * @param \ReflectionClass $cls
     */
    public function removeAllOfType(\ReflectionClass $cls)
    {
        $search = $cls->name . '@';

        $this->pool = Psi::it($this->pool)
            ->filterKey(function ($k) use ($search) {
                return strpos($k, $search) !== 0;
            })
            ->toArray();
    }
}
