<?php
/**
 * File was created 12.10.2015 06:49
 */

namespace PeekAndPoke\Component\Slumber\Tests\Mocks;

use PeekAndPoke\Component\Slumber\SlumberDependencies;
use Psr\Container\ContainerInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class UnitTestServiceProvider implements ContainerInterface
{
    /**
     * @param string $id
     *
     * @return bool
     */
    public function has($id)
    {
        return
            $id === SlumberDependencies::PUBLIC_REFERENCE_GENERATOR ||
            $id === SlumberDependencies::USER_RECORD_PROVIDER;
    }

    /**
     * @param string $id
     *
     * @return mixed
     * @throws \Exception
     */
    public function get($id)
    {
        switch ($id) {
            case SlumberDependencies::PUBLIC_REFERENCE_GENERATOR:
                return new UnitTestPublicReferenceGenerator();
            case SlumberDependencies::USER_RECORD_PROVIDER:
                return new UnitTestUserRecordProvider();
        }

        throw new \ErrorException('Service ' . $id . ' cannot be provided!');
    }
}
