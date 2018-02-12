<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 06.01.17
 * Time: 11:57
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use MongoDB\Collection;
use MongoDB\Driver\Exception\WriteException;
use PeekAndPoke\Component\Slumber\Data\AwakingCursorIterator;
use PeekAndPoke\Component\Slumber\Data\Cursor;
use PeekAndPoke\Component\Slumber\Data\EntityPool;
use PeekAndPoke\Component\Slumber\Data\Error\DuplicateError;
use PeekAndPoke\Component\Slumber\Data\MongoDb\Error\MongoDbDuplicateError;
use PeekAndPoke\Component\Slumber\Data\Result;
use PeekAndPoke\Component\Slumber\Data\StorageDriver;

/**
 * @api
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 *
 * @see    https://docs.mongodb.com/php-library/master/tutorial/
 */
class MongoDbStorageDriver implements StorageDriver
{
    /** @var EntityPool */
    private $entityPool;
    /** @var Collection */
    private $collection;
    /** @var MongoDbCodecSet */
    private $codecSet;
    /** @var \ReflectionClass */
    private $entityBaseClass;
    /** @var MongoDbEntityConfig */
    private $entityConfig;

    public function __construct(EntityPool $entityPool, MongoDbCodecSet $codecSet, Collection $collection, \ReflectionClass $entityBaseClass)
    {
        $this->entityPool      = $entityPool;
        $this->codecSet        = $codecSet;
        $this->collection      = $collection;
        $this->entityBaseClass = $entityBaseClass;
        $this->entityConfig    = $codecSet->getLookUp()->getEntityConfig($entityBaseClass);
    }

    /**
     * @return \ReflectionClass
     */
    public function getEntityBaseClass()
    {
        return $this->entityBaseClass;
    }

    /**
     * @return array
     */
    public function buildIndexes()
    {
        return $this->codecSet->getIndexer()->ensureIndexes($this->collection, $this->entityBaseClass);
    }

    /**
     * @param $query
     *
     * @return Cursor
     */
    public function find(array $query = null)
    {
        return $this->guard(
            function () use ($query) {
                return $this->findInternal($query);
            }
        );
    }

    /**
     * @param $query
     *
     * @return Cursor
     */
    private function findInternal(array $query = null)
    {
        $query = $query ?: [];

        // We do not provide the cursor right away.
        // By doing so we post pone the query until the data is really requested by iterating it.
        $cursorProvider = function ($options) use ($query) {

            // we want raw php arrays as return types
            $options['typeMap'] = ['root' => 'array', 'document' => 'array', 'array' => 'array'];

            return new AwakingCursorIterator(
                $this->collection->find($query, $options),
                $this->codecSet->getAwaker(),
                $this->entityBaseClass
            );
        };

        $countProvider = function ($options) use ($query) {
            return $this->collection->count($query, $options);
        };

        return new MongoDbCursor($cursorProvider, $countProvider);
    }

    /**
     * @param array|null $query
     *
     * @return null
     */
    public function findOne(array $query = null)
    {
        return $this->guard(
            function () use ($query) {
                return $this->findOneInternal($query);
            }
        );
    }

    /**
     * @param array|null $query
     *
     * @return null
     */
    private function findOneInternal(array $query = null)
    {
        // TODO: find a more encapsulated way for looking it up in the pool
        //       can we use the propertyAccess of the ID and use the propertyName? Is the alright ?

        // do we have it in the pool ?
        if (\count($query) === 1
            && isset($query['_id'])
            && $this->entityPool->has($this->entityBaseClass, EntityPool::PRIMARY_ID, (string) $query['_id'])
        ) {
            return $this->entityPool->get($this->entityBaseClass, EntityPool::PRIMARY_ID, (string) $query['_id']);
        }

        $result = $this->collection->findOne(
            $query ?: [],
            [
                'typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array'] // we want raw php arrays as return types
            ]
        );

        if ($result === null) {
            return null;
        }

        return $this->codecSet->getAwaker()->awake($result, $this->entityBaseClass);
    }

    /**
     * @param mixed $item
     *
     * @return Result\InsertOneResult
     *
     * @throws DuplicateError
     */
    public function insert($item)
    {
        return $this->guard(
            function () use ($item) {
                return $this->insertInternal($item);
            }
        );
    }

    /**
     * @param mixed $item
     *
     * @return Result\InsertOneResult
     *
     * @throws DuplicateError
     */
    private function insertInternal($item)
    {
        $slumbering = $this->codecSet->getSlumberer()->slumber($item);

        if (empty($slumbering['_id'])) {
            unset($slumbering['_id']);
        }

        $result = $this->collection->insertOne($slumbering);

        // write back the id
        $insertedId = (string) $result->getInsertedId();
        $this->setItemId($item, $insertedId);

        // set it on the entity pool
        $this->entityPool->set($this->entityBaseClass, EntityPool::PRIMARY_ID, $insertedId, $item);

        // dispatch post save events (e.g. for the Journal)
        $this->invokePostSaveListeners($item, $slumbering);

        return new Result\InsertOneResult($insertedId, $result->isAcknowledged());
    }

    /**
     * Insert an item
     *
     * @param mixed $item
     *
     * @return Result\SaveOneResult
     *
     * @throws DuplicateError
     */
    public function save($item)
    {
        return $this->guard(
            function () use ($item) {
                return $this->saveInternal($item);
            }
        );
    }

    /**
     * Insert an item
     *
     * @param mixed $item
     *
     * @return Result\SaveOneResult
     *
     * @throws DuplicateError
     */
    private function saveInternal($item)
    {
        $slumbering = $this->codecSet->getSlumberer()->slumber($item);

        ////  CREATE OR UPDATE ?  //////////////////////////////////////////////////////////////////////////////////////

        if (empty($slumbering['_id'])) {

            // unset the _id so we get an id created
            unset ($slumbering['_id']);

            try {
                $insertOneResult = $this->collection->insertOne($slumbering);
            } catch (WriteException $e) {
                throw MongoDbDuplicateError::from($e);
            }

            // write back the id
            $insertedId = (string) $insertOneResult->getInsertedId();
            $this->setItemId($item, $insertedId);

            // set it on the entity pool
            $this->entityPool->set($this->entityBaseClass, EntityPool::PRIMARY_ID, $insertedId, $item);

            // build return result
            $result = new Result\SaveOneResult($insertedId, $insertOneResult->isAcknowledged(), false);

        } else {
            // UPDATE or INSERT with specific id
            try {
                $updateOneResult = $this->collection->updateOne(
                    ['_id' => MongoDbUtil::ensureMongoId($slumbering['_id'])],
                    ['$set' => $slumbering],
                    ['upsert' => true]
                );
            } catch (WriteException $e) {
                throw MongoDbDuplicateError::from($e);
            }

            // save it on the entity pool
            $this->entityPool->set($this->entityBaseClass, EntityPool::PRIMARY_ID, $slumbering['_id'], $item);

            // build return result
            $result = new Result\SaveOneResult(
                $updateOneResult->getUpsertedId(),
                $updateOneResult->isAcknowledged(),
                $updateOneResult->getUpsertedCount() === 1
            );
        }

        // dispatch post save events (e.g. for the Journal)
        $this->invokePostSaveListeners($item, $slumbering);

        return $result;
    }

    /**
     * @param mixed $entity
     *
     * @return Result\RemoveResult
     */
    public function remove($entity)
    {
        return $this->guard(
            function () use ($entity) {
                return $this->removeInternal($entity);
            }
        );
    }

    /**
     * @param mixed $entity
     *
     * @return Result\RemoveResult
     */
    private function removeInternal($entity)
    {
        $id = $this->getItemId($entity);

        $result = $this->collection->deleteOne([
            '_id' => MongoDbUtil::ensureMongoId($id),
        ]);

        return new Result\RemoveResult($result->getDeletedCount(), $result->isAcknowledged());
    }

    /**
     * Remove all from this collection
     *
     * @param array|null $query
     *
     * @return Result\RemoveResult
     */
    public function removeAll(array $query = null)
    {
        return $this->guard(
            function () use ($query) {
                return $this->removeAllInternal($query);
            }
        );
    }

    /**
     * Remove all from this collection
     *
     * @param array|null $query
     *
     * @return Result\RemoveResult
     */
    public function removeAllInternal(array $query = null)
    {
        if ($query === null) {
            return new Result\RemoveResult(0, false);
        }

        $result = $this->collection->deleteMany($query);

        return new Result\RemoveResult($result->getDeletedCount(), $result->isAcknowledged());
    }

    /**
     * @param mixed $item
     *
     * @return mixed
     */
    private function getItemId($item)
    {
        return $this->entityConfig->getIdAccess()->get($item);
    }

    /**
     * @param mixed $entity
     * @param mixed $id
     */
    private function setItemId($entity, $id)
    {
        $this->entityConfig->getIdAccess()->set($entity, $id);
    }

    /**
     * @param mixed $item       The item that was saved
     * @param array $slumbering The serialized data
     */
    private function invokePostSaveListeners($item, $slumbering)
    {
        // dispatch post save events (e.g. for the Journal)
        if ($this->entityConfig->hasPostSaveClassListeners()) {

            $postSaveEvent = $this->codecSet->createPostSaveEventFor($item, $slumbering);

            foreach ($this->entityConfig->getPostSaveClassListeners() as $postSave) {
                $postSave->execute($postSaveEvent);
            }
        }
    }

    private function guard(callable $action)
    {
        return MongoDbGuard::guard($action);
    }
}
