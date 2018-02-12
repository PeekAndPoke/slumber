<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 30.03.17
 * Time: 08:18
 */

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
    /** @var RepositoryRegistry */
    private $registry;

    /**
     * StorageImpl constructor.
     *
     * @param EntityPool         $entityPool
     * @param RepositoryRegistry $registry
     */
    public function __construct(EntityPool $entityPool, RepositoryRegistry $registry)
    {
        $this->entityPool = $entityPool;
        $this->registry   = $registry;
    }

    /**
     * @param mixed $subject
     *
     * @throws SlumberException When no repo is associated with the subject
     */
    public function save($subject)
    {
        if (! \is_object($subject)) {
            return;
        }

        $repo = $this->getRepositoryByEntity($subject);

        if ($repo === null) {
            throw new SlumberRuntimeException('No repository is associated with objects of type "' . \get_class($subject) . '"');
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
        if (! \is_object($subject)) {
            return;
        }

        $repo = $this->getRepositoryByEntity($subject);

        if ($repo === null) {
            throw new SlumberRuntimeException('No repository is associated with objects of type "' . \get_class($subject) . '"');
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
        return $this->registry->getRepositories();
    }

    /**
     * @param string $name
     *
     * @return Repository|null
     */
    public function getRepositoryByName($name)
    {
        return $this->registry->getRepositoryByName($name);
    }

    /**
     * @param string $cls
     *
     * @return bool
     */
    public function hasRepositoryByClassName($cls)
    {
        return $this->registry->hasRepositoryByClassName($cls);
    }

    /**
     * @param string $cls
     *
     * @return Repository|null
     */
    public function getRepositoryByClassName($cls)
    {
        return $this->registry->getRepositoryByClassName($cls);
    }

    /**
     * @param mixed $entity
     *
     * @return Repository|null
     */
    public function getRepositoryByEntity($entity)
    {
        return $this->registry->getRepositoryByEntity($entity);
    }
}
