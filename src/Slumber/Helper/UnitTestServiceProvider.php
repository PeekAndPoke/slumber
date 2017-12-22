<?php
/**
 * File was created 12.10.2015 06:49
 */

namespace PeekAndPoke\Component\Slumber\Helper;

use PeekAndPoke\Component\Slumber\SlumberDependencies;
use Psr\Container\ContainerInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class UnitTestServiceProvider implements ContainerInterface
{
    /** @var array */
    private $services;

    public function __construct()
    {
        $this->services = [
            SlumberDependencies::PUBLIC_REFERENCE_GENERATOR => new UnitTestPublicReferenceGenerator(),

            SlumberDependencies::USER_RECORD_PROVIDER => new UnitTestUserRecordProvider(),
        ];
    }

    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id)
    {
        return isset($this->services[$id]);
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($id)
    {
        if (isset($this->services[$id])) {
            return $this->services[$id];
        }

        throw new \ErrorException('Service ' . $id . ' cannot be provided!');
    }

    /**
     * @param string $id
     * @param mixed  $service
     *
     * @return $this
     */
    public function set($id, $service)
    {
        $this->services[$id] = $service;

        return $this;
    }
}
