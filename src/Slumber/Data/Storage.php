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
    public function getEntityPool();

    /**
     * @return Repository[]
     */
    public function getRepositories();

    /**
     * @param string $name
     *
     * @return Repository|null
     */
    public function getRepositoryByName($name);

    /**
     * @param string $cls
     *
     * @return bool
     */
    public function hasRepositoryByClassName($cls);

    /**
     * @param string $cls
     *
     * @return Repository|null
     */
    public function getRepositoryByClassName($cls);

    /**
     * @param mixed $entity
     *
     * @return Repository|null
     */
    public function getRepositoryByEntity($entity);
}
