<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 06.01.17
 * Time: 11:51
 */

namespace PeekAndPoke\Component\Slumber\Data;

use PeekAndPoke\Component\Slumber\Data\Error\DuplicateError;

/**
 * @api
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface StorageDriver
{
    /**
     * @return \ReflectionClass
     */
    public function getEntityBaseClass();

    /**
     * @return mixed // TODO: better return type
     */
    public function buildIndexes();

    /**
     * Insert an item
     *
     * @param mixed $item
     *
     * @return Result\InsertOneResult
     *
     * @throws DuplicateError
     */
    public function insert($item);

    /**
     * Insert OR update an item
     *
     * @param mixed $item
     *
     * @return Result\SaveOneResult
     *
     * @throws DuplicateError
     */
    public function save($item);

    /**
     * @param $entity
     *
     * @return Result\RemoveResult
     */
    public function remove($entity);

    /**
     * Remove all from this collection that match the query.
     *
     * If the query is not set nothing will be deleted.
     *
     * @param array $query
     *
     * @return Result\RemoveResult
     */
    public function removeAll(array $query = null);

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
}
