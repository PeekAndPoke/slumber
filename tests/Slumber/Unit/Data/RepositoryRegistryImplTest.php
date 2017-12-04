<?php
/**
 * Created by gerk on 04.12.17 06:42
 */

namespace PeekAndPoke\Component\Slumber\Unit\Data;

use PeekAndPoke\Component\Slumber\Data\Repository;
use PeekAndPoke\Component\Slumber\Data\RepositoryRegistryImpl;
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
        $subject = new RepositoryRegistryImpl();

        $subject->registerProvider('name', [UnitTestMainClass::class], function () {
            return $this->getMockBuilder(Repository::class)->getMock();
        });

        $created = $subject->getRepositoryByName('name');

        $this->assertInstanceOf(Repository::class, $created);

        $created2 = $subject->getRepositoryByName('name');

        $this->assertSame($created, $created2, 'A repo must be create only ones and then be re-used');
    }
}
