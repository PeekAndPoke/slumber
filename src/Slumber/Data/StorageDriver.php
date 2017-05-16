<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 06.01.17
 * Time: 11:51
 */
declare(strict_types=1);

namespace PeekAndPoke\Component\Slumber\Data;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface StorageDriver
{
    /**
     * @return \ReflectionClass
     */
    public function getEntityBaseClass() : \ReflectionClass;

    /**
     * @return mixed // TODO: better return type
     */
    public function buildIndexes();

    /**
     * Insert an item
     *
     * @param mixed $item
     *
     * @return mixed     // TODO: better return type
     */
    public function insert($item);

    /**
     * Insert OR update an item
     *
     * @param mixed $item
     *
     * @return mixed     // TODO: better return type
     */
    public function save($item);

    /**
     * @param $entity
     *
     * @return mixed    // TODO: better return type
     */
    public function remove($entity);

    /**
     * Remove all from this collection
     *
     * @return mixed    // TODO: better return type
     */
    public function removeAll();

    /**
     * @param $query
     *
     * @return Cursor
     */
    public function find(array $query = null) : Cursor;

    /**
     * @param array|null $query
     *
     * @return mixed|null
     */
    public function findOne(array $query = null);
}
