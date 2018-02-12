<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 06.01.17
 * Time: 11:50
 */

namespace PeekAndPoke\Component\Slumber\Data;

use PeekAndPoke\Component\Slumber\Core\Exception\SlumberRuntimeException;
use PeekAndPoke\Component\Slumber\Data\Error\DuplicateError;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbUtil;
use PeekAndPoke\Component\Slumber\Data\Result\InsertOneResult;
use PeekAndPoke\Component\Slumber\Data\Result\SaveOneResult;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class EntityRepository implements Repository
{
    /** @var string */
    private $name;
    /** @var StorageDriver */
    private $driver;

    public function __construct($name, StorageDriver $driver)
    {
        $this->name   = $name;
        $this->driver = $driver;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return \ReflectionClass
     */
    public function getEntityClass()
    {
        return $this->driver->getEntityBaseClass();
    }

    /**
     * TODO: remove me! It was only used to find a repo by sub-classes ... we no longer need this
     * @return string[]
     */
    public function getEntityClassAliases()
    {
        return [];
    }

    /**
     * @return mixed
     */
    public function buildIndexes()
    {
        return $this->driver->buildIndexes();
    }

    /**
     * Insert a database entry
     *
     * @param mixed $subject
     *
     * @return InsertOneResult
     *
     * @throws SlumberRuntimeException When the given subject cannot be stored in this repository
     * @throws DuplicateError          When the underlying database permits the insert, due to a duplicate key exception
     */
    public function insert($subject)
    {
        if ($subject === null || ! \is_object($subject)) {
            return null;
        }

        $this->checkObjectCompatibility($subject);

        return $this->driver->insert($subject);
    }

    /**
     * @param mixed $subject
     *
     * @return SaveOneResult
     *
     * @throws SlumberRuntimeException When the given subject cannot be stored in this repository
     * @throws DuplicateError          When the underlying database permits the insert, due to a duplicate key exception
     */
    public function save($subject)
    {
        if ($subject === null || ! \is_object($subject)) {
            return null;
        }

        $this->checkObjectCompatibility($subject);

        return $this->driver->save($subject);
    }

    /**
     * @param $query
     *
     * @return Cursor
     */
    public function find(array $query = null)
    {
        return $this->driver->find($query);
    }

    /**
     * @param array|null $query
     *
     * @return mixed|null
     */
    public function findOne(array $query = null)
    {
        return $this->driver->findOne($query);
    }

    /**
     * @param mixed $id
     *
     * @return mixed|null
     */
    public function findById($id)
    {
        return $this->findOne([
            '_id' => MongoDbUtil::ensureMongoId($id),
        ]);
    }

    /**
     * Find one object by its public reference
     *
     * @param string $reference
     *
     * @return mixed|null
     */
    public function findByReference($reference)
    {
        return $this->findOne([
            'reference' => $reference,
        ]);
    }

    /**
     * @param $entity
     *
     * @return Result\RemoveResult
     */
    public function remove($entity)
    {
        return $this->driver->remove($entity);
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
        return $this->driver->removeAll($query);
    }

    /**
     * @param $subject
     *
     * @throw SlumberRuntimeException
     */
    private function checkObjectCompatibility($subject)
    {
        $entityClassName = $this->driver->getEntityBaseClass()->name;

        $isOk = \is_object($subject) && $subject instanceof $entityClassName;

        if (! $isOk) {
            throw new SlumberRuntimeException(
                'Repository ' . \get_class($this) . ' can only store objects of type ' . $entityClassName .
                ' but a ' . \get_class($subject) . ' was given!'
            );
        }
    }
}
