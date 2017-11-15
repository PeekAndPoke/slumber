<?php
/**
 * Created by gerk on 15.11.17 21:11
 */

namespace PeekAndPoke\Component\PropertyAccess\Unit;

use PeekAndPoke\Component\PropertyAccess\ReflectionPropertyAccess;
use PeekAndPoke\Component\PropertyAccess\Stubs\UnitTestPropertyAccessMainClass;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ReflectionPropertyAccessTest extends TestCase
{
    public function testGet()
    {
        $object = new UnitTestPropertyAccessMainClass();
        $object->setProtectedProp('val');

        $subject = ReflectionPropertyAccess::create(new \ReflectionClass($object), 'protectedProp');

        $this->assertSame(
            'val',
            $subject->get($object),
            'Getting a property must work correctly'
        );
    }

    public function testSet()
    {
        $object = new UnitTestPropertyAccessMainClass();

        $subject = ReflectionPropertyAccess::create(new \ReflectionClass($object), 'protectedProp');
        $subject->set($object, 'val');

        $this->assertSame(
            'val',
            $object->getProtectedProp(),
            'Setting a property must work correctly'
        );
    }

    public function testSerializeAndUnserialize()
    {
        $object = new UnitTestPropertyAccessMainClass();

        // create accessors
        $prop1 = ReflectionPropertyAccess::create(new \ReflectionClass($object), 'prop1');

        // serialize and unserialize the accessors
        /** @var ReflectionPropertyAccess $prop1Revived */
        $prop1Revived = unserialize(serialize($prop1));

        // assert that the revived instances will get values correctly
        $object->setProp1('prop1');

        $this->assertSame('prop1', $prop1Revived->get($object), 'Getting must work after reviving');

        // assert that the revived instances will get values correctly
        $prop1Revived->set($object, 'set1');

        $this->assertSame('set1', $object->getProp1(), 'Setting must work after reviving');
    }
}
