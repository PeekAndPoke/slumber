<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 30.03.17
 * Time: 08:18
 */
declare(strict_types=1);

namespace PeekAndPoke\Component\Slumber\Data;

use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;
use PeekAndPoke\Component\Slumber\Core\Exception\SlumberRuntimeException;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class StorageImpl implements Storage
{
    /** @var EntityPool */
    private $entityPool;

    /** @var Repository[] */
    private $repositories = [];

    /**
     * StorageImpl constructor.
     *
     * @param EntityPool $entityPool
     */
    public function __construct(EntityPool $entityPool)
    {
        $this->entityPool = $entityPool;
    }

    /**
     * @param Repository $repository
     *
     * @return StorageImpl
     */
    public function addRepository(Repository $repository)
    {
        $this->repositories[] = $repository;

        return $this;
    }

    /**
     * @param mixed $subject
     *
     * @throws SlumberException When no repo is associated with the subject
     */
    public function save($subject)
    {
        if (! is_object($subject)) {
            return;
        }

        $repo = $this->getRepositoryByEntity($subject);

        if ($repo === null) {
            throw new SlumberRuntimeException('No repository is associated with objects of type "' . get_class($subject) . '"');
        }

        $repo->save($subject);
    }

    /**
     * @param mixed $subject
     *
     * @throws SlumberException When no repo is associated with the subject
     */
    public function remove($subject)
    {
        if (! is_object($subject)) {
            return;
        }

        $repo = $this->getRepositoryByEntity($subject);

        if ($repo === null) {
            throw new SlumberRuntimeException('No repository is associated with objects of type "' . get_class($subject) . '"');
        }

        $repo->remove($subject);
    }

    /**
     * @return EntityPool
     */
    public function getEntityPool()
    {
        return $this->entityPool;
    }

    /**
     * @return Repository[]
     */
    public function getRepositories()
    {
        return $this->repositories;
    }

    /**
     * @param string $name
     *
     * @return Repository|null
     */
    public function getRepositoryByName($name)
    {
        foreach ($this->repositories as $repository) {
            if ($repository->getName() === $name) {
                return $repository;
            }
        }

        return null;
    }

    /**
     * @param string $cls
     *
     * @return bool
     */
    public function hasRepositoryByClassName($cls)
    {
        return $this->getRepositoryByClassName($cls) !== null;
    }

    /**
     * @param string $cls
     *
     * @return Repository|null
     */
    public function getRepositoryByClassName($cls)
    {
        foreach ($this->repositories as $repository) {
            if ($repository->getEntityClass()->getName() === $cls ||
                in_array($cls, $repository->getEntityClassAliases(), true)
            ) {
                return $repository;
            }
        }

        return null;
    }

    /**
     * @param mixed $entity
     *
     * @return Repository|null
     */
    public function getRepositoryByEntity($entity)
    {
        if (! is_object($entity)) {
            return null;
        }

        return $this->getRepositoryByClassName(get_class($entity));
    }
}
