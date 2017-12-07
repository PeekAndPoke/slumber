<?php
/**
 * Created by gerk on 04.12.17 05:51
 */

namespace PeekAndPoke\Component\Slumber\Data;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface RepositoryRegistry
{
    /**
     * Registers a repositories with its table name and all class names stored in the repository
     *
     * This is an optimization. It allows to register repositories without the need to autoload the repository class upfront.
     * The repository class will only be loaded once the provider is called.
     * This helps to speed up requests when the number of repositories grow considerably big.
     *
     * @param string   $name
     * @param array    $classes
     * @param \Closure $provider
     *
     * @return $this
     */
    public function registerProvider($name, array $classes, \Closure $provider);

    /**
     * @return Repository[]
     */
    public function getRepositories();

    /**
     * @param string $class
     *
     * @return bool
     */
    public function hasRepositoryByClassName($class);

    /**
     * @param string $class
     *
     * @return null|Repository
     */
    public function getRepositoryByClassName($class);

    /**
     * @param mixed $entity
     *
     * @return null|Repository
     */
    public function getRepositoryByEntity($entity);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasRepositoryByName($name);

    /**
     * @param string $name
     *
     * @return null|Repository
     */
    public function getRepositoryByName($name);
}
