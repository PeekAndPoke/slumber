<?php
/**
 * Created by gerk on 04.12.17 05:54
 */

namespace PeekAndPoke\Component\Slumber\Data;

use PeekAndPoke\Component\Psi\Psi;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class RepositoryRegistryImpl implements RepositoryRegistry
{
    /**
     * @var \SplObjectStorage
     */
    private $repositoriesByProviders;

    /**
     * Maps tables name to repository providers
     *
     * @var \Closure
     */
    private $providersByNames = [];

    /**
     * Maps class names to repository providers
     *
     * @var \Closure
     */
    private $providersByClassNames = [];

    /**
     */
    public function __construct()
    {
        $this->repositoriesByProviders = new \SplObjectStorage();
    }

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
    public function registerProvider($name, array $classes, \Closure $provider)
    {
        $realProvider = function () use ($name, $classes, $provider) {
            $context = new RepositoryRegistry\ProviderContext($name, $classes);

            return $provider($context);
        };

        $this->providersByNames[$name] = $realProvider;

        foreach ($classes as $class) {
            $this->providersByClassNames[$class] = $realProvider;
        }

        return $this;
    }

    /**
     * @return Repository[]
     */
    public function getRepositories()
    {
        return Psi::it($this->providersByNames)
            ->filter(new Psi\IsInstanceOf(\Closure::class))
            ->map(function (\Closure $c) { return $this->resolveProvider($c); })
            ->toArray();
    }

    /**
     * @param string $class
     *
     * @return bool
     */
    public function hasRepositoryByClassName($class)
    {
        return isset($this->providersByClassNames[$class]);
    }

    /**
     * @param string $class
     *
     * @return null|Repository
     */
    public function getRepositoryByClassName($class)
    {
        return isset($this->providersByClassNames[$class])
            ? $this->resolveProvider($this->providersByClassNames[$class])
            : null;
    }

    /**
     * @param mixed $entity
     *
     * @return null|Repository
     */
    public function getRepositoryByEntity($entity)
    {
        if (! \is_object($entity)) {
            return null;
        }

        return $this->getRepositoryByClassName(\get_class($entity));
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasRepositoryByName($name)
    {
        return isset($this->providersByNames[$name]);
    }

    /**
     * @param string $name
     *
     * @return null|Repository
     */
    public function getRepositoryByName($name)
    {
        return isset($this->providersByNames[$name])
            ? $this->resolveProvider($this->providersByNames[$name])
            : null;
    }

    /**
     * @param \Closure|null $provider
     *
     * @return null|Repository
     */
    private function resolveProvider(\Closure $provider = null)
    {
        if ($provider === null) {
            return null;
        }

        if ($this->repositoriesByProviders->contains($provider)) {
            return $this->repositoriesByProviders->offsetGet($provider);
        }

        $createdRepo = $provider();

        $this->repositoriesByProviders->offsetSet($provider, $createdRepo);

        return $createdRepo;
    }
}
