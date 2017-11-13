<?php
/**
 * Created by gerk on 13.11.17 16:35
 */

namespace PeekAndPoke\Component\PropertyAccess\Unit;

use PeekAndPoke\Component\PropertyAccess\PublicPropertyAccess;
use PeekAndPoke\Component\PropertyAccess\Stubs\UnitTestScopedPropertyAccessMainClass;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class PublicPropertyAccessTest extends TestCase
{
    public function testGet()
    {
        $object = new UnitTestScopedPropertyAccessMainClass();
        $object->setPublicProp('public1');
        $object->setPublicPropOnBase('public2');

        $public1 = PublicPropertyAccess::create('publicProp');
        $public2 = PublicPropertyAccess::create('publicPropOnBase');

        $this->assertSame('public1', $public1->get($object), 'Getting public property must work');
        $this->assertSame('public2', $public2->get($object), 'Getting public property of base class must work');
    }

    public function testSet()
    {
        $object = new UnitTestScopedPropertyAccessMainClass();

        $public1 = PublicPropertyAccess::create('publicProp');
        $public2 = PublicPropertyAccess::create('publicPropOnBase');

        $public1->set($object, 'public1');
        $public2->set($object, 'public2');

        $this->assertSame('public1', $object->getPublicProp(), 'Setting public property must work');
        $this->assertSame('public2', $object->getPublicPropOnBase(), 'Setting public property of base class must work');
    }

}
