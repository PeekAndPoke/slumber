<?php
/**
 * File was created 11.10.2015 13:32
 */

namespace PeekAndPoke\Component\Slumber\Data;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface EntityPool
{
    public const PRIMARY_ID = '__id__';

    /**
     * @return array
     */
    public function all();

    /**
     * @return EntityPoolStats
     */
    public function stats();

    ////  GET SET CHECK  ///////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param \ReflectionClass $cls
     * @param string           $idKey
     * @param mixed            $idValue
     *
     * @return bool
     */
    public function has(\ReflectionClass $cls, $idKey, $idValue);

    /**
     * @param \ReflectionClass $cls
     * @param string           $idKey
     * @param mixed            $idValue
     *
     * @return mixed
     */
    public function get(\ReflectionClass $cls, $idKey, $idValue);

    /**
     * @param \ReflectionClass $cls
     * @param string           $idKey
     * @param mixed            $idValue
     * @param mixed            $value
     */
    public function set(\ReflectionClass $cls, $idKey, $idValue, $value);

    ////  REMOVING ITEMS  //////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Remove all entities from the pool
     */
    public function clear();

    /**
     * @param \ReflectionClass $cls
     * @param string           $idKey
     * @param string           $idValue
     */
    public function remove(\ReflectionClass $cls, $idKey, $idValue);

    /**
     * @param \ReflectionClass $cls
     */
    public function removeAllOfType(\ReflectionClass $cls);
}
