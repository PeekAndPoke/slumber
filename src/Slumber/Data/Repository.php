<?php
/**
 * File was created 05.10.2015 06:29
 */

namespace PeekAndPoke\Component\Slumber\Data;

use PeekAndPoke\Component\Slumber\Core\Exception\SlumberRuntimeException;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface Repository
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return \ReflectionClass
     */
    public function getEntityClass();

    /**
     * @return string[]
     */
    public function getEntityClassAliases();

    /**
     * @return mixed
     */
    public function buildIndexes();

    ////  ADDING ENTITIES  /////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Insert a database entry
     *
     * @param mixed $item
     *
     * @return mixed|null
     */
    public function insert($item);

    /**
     * Insert OR update a database entry
     *
     * @param mixed $item
     *
     * @return array|null
     *
     * @throws SlumberRuntimeException
     */
    public function save($item);

    ////  RETRIEVING ENTITIES  /////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param $query
     *
     * @return Cursor
     */
    public function find(array $query = null);

    /**
     * @param array|null $query
     *
     * @return mixed|null
     */
    public function findOne(array $query = null);

    /**
     * @param mixed $id
     *
     * @return mixed|null
     */
    public function findById($id);

    /**
     * Find one object by its public reference
     *
     * @param string $reference
     *
     * @return mixed|null
     */
    public function findByReference($reference);

    ////  REMOVING ENTITIES  ///////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param $entity
     */
    public function remove($entity);

    /**
     * Remove all from this collection
     */
    public function removeAll();
}
