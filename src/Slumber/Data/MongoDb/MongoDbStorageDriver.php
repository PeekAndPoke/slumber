<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 06.01.17
 * Time: 11:57
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use MongoDB;
use PeekAndPoke\Component\Slumber\Data\AwakingCursorIterator;
use PeekAndPoke\Component\Slumber\Data\Cursor;
use PeekAndPoke\Component\Slumber\Data\EntityPool;
use PeekAndPoke\Component\Slumber\Data\StorageDriver;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 *
 * @see    https://docs.mongodb.com/php-library/master/tutorial/
 */
class MongoDbStorageDriver implements StorageDriver
{
    /** @var EntityPool */
    private $entityPool;
    /** @var MongoDB\Collection */
    private $collection;
    /** @var MongoDbCodecSet */
    private $codecSet;
    /** @var \ReflectionClass */
    private $entityBaseClass;
    /** @var MongoDbEntityConfig */
    private $entityConfig;

    public function __construct(EntityPool $entityPool, MongoDbCodecSet $codecSet, MongoDB\Collection $collection, \ReflectionClass $entityBaseClass)
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
     * @param mixed $item
     *
     * @return MongoDB\InsertOneResult
     */
    public function insert($item)
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

        // TODO: transform the result
        return $result;
    }

    /**
     * Insert an item
     *
     * @param mixed $item
     *
     * @return mixed     // TODO: better return type
     */
    public function save($item)
    {
        $slumbering = $this->codecSet->getSlumberer()->slumber($item);

        ////  CREATE OR UPDATE ?  //////////////////////////////////////////////////////////////////////////////////////

        if (empty($slumbering['_id'])) {

            // unset the _id so we get an id created
            unset ($slumbering['_id']);

            // TODO: transform the result
            $result = $this->collection->insertOne($slumbering);

            // write back the id
            $insertedId = (string) $result->getInsertedId();
            $this->setItemId($item, $insertedId);

            // set it on the entity pool
            $this->entityPool->set($this->entityBaseClass, EntityPool::PRIMARY_ID, $insertedId, $item);
        } else {
            // UPDATE or INSERT with specific id

            // TODO: transform the result
            $result = $this->collection->updateOne(
                ['_id' => MongoDbUtil::ensureMongoId($slumbering['_id'])],
                ['$set' => $slumbering],
                ['upsert' => true]
            );

            $this->entityPool->set($this->entityBaseClass, EntityPool::PRIMARY_ID, $slumbering['_id'], $item);
        }

        // dispatch post save events (e.g. for the Journal)
        $this->invokePostSaveListeners($item, $slumbering);

        return $result;
    }

    /**
     * @param mixed $entity
     *
     * @return mixed    // TODO: better return type
     */
    public function remove($entity)
    {
        // TODO: we cannot know that getId exists
        $result = $this->collection->deleteOne([
            '_id' => MongoDbUtil::ensureMongoId(
                $this->getItemId($entity)
            ),
        ]);

        return $result;
    }

    /**
     * Remove all from this collection
     *
     * @return mixed    // TODO: better return type
     */
    public function removeAll()
    {
        return $this->collection->deleteMany([]);
    }

    /**
     * @param $query
     *
     * @return Cursor
     */
    public function find(array $query = null)
    {
        $query = $query ?: [];

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
        // TODO: find a more encapsulated way for looking it up in the pool
        // do we have it in the pool ?
        if (count($query) === 1
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
     * @return mixed
     */
    private function getItemId($item)
    {
        // TODO: we cannot know that getId() exists ... we need to read it from the EntityConfig
        return $item->getId();
    }

    /**
     * @param mixed $entity
     * @param mixed $id
     */
    private function setItemId($entity, $id)
    {
        // TODO: we cannot know that setId() exists ... we need to read it from the EntityConfig
        $entity->setId($id);
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
}
