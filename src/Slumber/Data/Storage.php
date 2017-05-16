<?php
/**
 * File was created 15.03.2016 06:39
 */

namespace PeekAndPoke\Component\Slumber\Data;

use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface Storage
{
    ////  SHORTCUTS  ///////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param mixed $subject
     *
     * @throws SlumberException When no repo is associated with the subject
     */
    public function save($subject);

    /**
     * @param mixed $subject
     *
     * @throws SlumberException When no repo is associated with the subject
     */
    public function remove($subject);


    ////  RETRIEVE INFORMATION  ////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @return EntityPool
     */
    public function getEntityPool() : EntityPool;

    /**
     * @return Repository[]
     */
    public function getRepositories() : array;

    /**
     * @param string $name
     *
     * @return Repository|null
     */
    public function getRepositoryByName(string $name) : ?Repository;

    /**
     * @param string $cls
     *
     * @return bool
     */
    public function hasRepositoryByClassName(string $cls) : bool;

    /**
     * @param string $cls
     *
     * @return Repository|null
     */
    public function getRepositoryByClassName(string $cls) : ?Repository;

    /**
     * @param mixed $entity
     *
     * @return Repository|null
     */
    public function getRepositoryByEntity($entity) : ?Repository;
}
