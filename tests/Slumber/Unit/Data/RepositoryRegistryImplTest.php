<?php
/**
 * Created by gerk on 04.12.17 06:42
 */

namespace PeekAndPoke\Component\Slumber\Unit\Data;

use PeekAndPoke\Component\Slumber\Data\Repository;
use PeekAndPoke\Component\Slumber\Data\RepositoryRegistryImpl;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestAggregatedClass;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestMainClass;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class RepositoryRegistryImplTest extends TestCase
{

    public function testGetRepositoryByName()
    {
        $repo1 = null;
        $repo2 = null;

        $subject = new RepositoryRegistryImpl();

        $subject->registerProvider('repo1', [UnitTestMainClass::class], function () use (&$repo1) {
            $repo1 = $this->getMockBuilder(Repository::class)->getMock();

            return $repo1;
        });

        $subject->registerProvider('repo2', [UnitTestAggregatedClass::class], function () use (&$repo2) {
            $repo2 = $this->getMockBuilder(Repository::class)->getMock();

            return $repo2;
        });


        $createdRepo1 = $subject->getRepositoryByName('repo1');

        $this->assertInstanceOf(Repository::class, $createdRepo1);
        $this->assertSame($repo1, $createdRepo1);


        $createdRepo2 = $subject->getRepositoryByName('repo2');

        $this->assertInstanceOf(Repository::class, $createdRepo2);
        $this->assertSame($repo2, $createdRepo2);


        $createdRepo1Again = $subject->getRepositoryByName('repo1');

        $this->assertSame($createdRepo1, $createdRepo1Again, 'A repo must be created only ones and then be re-used');


        $createdRepo2Again = $subject->getRepositoryByName('repo2');

        $this->assertSame($createdRepo2, $createdRepo2Again, 'A repo must be created only ones and then be re-used');
    }

    public function testReposAreNotInstantiatedUntilTheirAreNeeded()
    {
        $repo1 = null;
        $repo2 = null;

        $subject = new RepositoryRegistryImpl();

        $subject->registerProvider('repo1', [UnitTestMainClass::class], function () use (&$repo1) {
            $repo1 = $this->getMockBuilder(Repository::class)->getMock();

            return $repo1;
        });

        $subject->registerProvider('repo2', [UnitTestAggregatedClass::class], function () use (&$repo2) {
            $repo2 = $this->getMockBuilder(Repository::class)->getMock();

            return $repo2;
        });

        $this->assertNull($repo1, 'The repo must not yet be created, as it was not requested yet');

        $this->assertNull($repo2, 'The repo must not yet be created, as it was not requested yet');
    }

    public function testGetRepositoryByNameAndClassReturnsTheSameInstance()
    {
        $repo1 = null;
        $repo2 = null;

        $subject = new RepositoryRegistryImpl();

        $subject->registerProvider('repo1', [UnitTestMainClass::class], function () use (&$repo1) {
            $repo1 = $this->getMockBuilder(Repository::class)->getMock();

            return $repo1;
        });

        $subject->registerProvider('repo2', [UnitTestAggregatedClass::class], function () use (&$repo2) {
            $repo2 = $this->getMockBuilder(Repository::class)->getMock();

            return $repo2;
        });

        $repo1ByName = $subject->getRepositoryByName('repo1');

        $repo1ByClass = $subject->getRepositoryByClassName(UnitTestMainClass::class);

        $this->assertSame($repo1ByName, $repo1ByClass);
    }

    public function testGetRepositoryByNameReturnNullWhenNotFound()
    {
        $repo1 = null;
        $repo2 = null;

        $subject = new RepositoryRegistryImpl();

        $subject->registerProvider('repo1', [UnitTestMainClass::class], function () use (&$repo1) {
            $repo1 = $this->getMockBuilder(Repository::class)->getMock();

            return $repo1;
        });

        $subject->registerProvider('repo2', [UnitTestAggregatedClass::class], function () use (&$repo2) {
            $repo2 = $this->getMockBuilder(Repository::class)->getMock();

            return $repo2;
        });

        $this->assertNull($subject->getRepositoryByName('UNKNOWN'), 'Null must be returned');
    }
}
