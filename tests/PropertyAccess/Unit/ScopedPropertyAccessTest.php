<?php
/**
 * Created by gerk on 13.11.17 06:15
 */

namespace PeekAndPoke\Component\PropertyAccess\Unit;

use PeekAndPoke\Component\PropertyAccess\ScopedPropertyAccess;
use PeekAndPoke\Component\PropertyAccess\Stubs\UnitTestPropertyAccessBaseClass;
use PeekAndPoke\Component\PropertyAccess\Stubs\UnitTestPropertyAccessMainClass;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ScopedPropertyAccessTest extends TestCase
{
    /**
     * Test that setting values through the accessor works
     */
    public function testGet()
    {
        $object = new UnitTestPropertyAccessMainClass();

        // set properties on the class itself
        $object->setProp1('val1');
        $object->setProp2('val2');
        // create accessors for these
        $prop1 = ScopedPropertyAccess::create(UnitTestPropertyAccessMainClass::class, 'prop1');
        $prop2 = ScopedPropertyAccess::create(UnitTestPropertyAccessMainClass::class, 'prop2');

        // set the properties on the base class with the same names
        $object->setProp1Shadowed('val1Shadowed');
        $object->setProp2Shadowed('val2Shadowed');
        // create accessors for theses
        $prop1Shadowed = ScopedPropertyAccess::create(UnitTestPropertyAccessBaseClass::class, 'prop1');
        $prop2Shadowed = ScopedPropertyAccess::create(UnitTestPropertyAccessBaseClass::class, 'prop2');

        // assert main properties are read correctly
        $this->assertSame('val1', $prop1->get($object), 'Getting a property must work');
        $this->assertSame('val2', $prop2->get($object), 'Getting another property must work');

        // assert shadowed properties are read correctly
        $this->assertSame('val1Shadowed', $prop1Shadowed->get($object), 'Getting a shadowed property must work');
        $this->assertSame('val2Shadowed', $prop2Shadowed->get($object), 'Getting another shadowed property must work');
    }

    public function testSet()
    {
        $object = new UnitTestPropertyAccessMainClass();

        // create access to main properties
        $prop1 = ScopedPropertyAccess::create(UnitTestPropertyAccessMainClass::class, 'prop1');
        $prop2 = ScopedPropertyAccess::create(UnitTestPropertyAccessMainClass::class, 'prop2');
        // set values
        $prop1->set($object, 'val1');
        $prop2->set($object, 'val2');

        // create access to shadowed properties
        $prop1Shadowed = ScopedPropertyAccess::create(UnitTestPropertyAccessBaseClass::class, 'prop1');
        $prop2Shadowed = ScopedPropertyAccess::create(UnitTestPropertyAccessBaseClass::class, 'prop2');
        // set values
        $prop1Shadowed->set($object, 'val1Shadowed');
        $prop2Shadowed->set($object, 'val2Shadowed');

        // assert main properties where set correctly
        $this->assertSame('val1', $object->getProp1(), 'Getting a property must work');
        $this->assertSame('val2', $object->getProp2(), 'Getting another property must work');

        // assert shadowed properties where set correctly
        $this->assertSame('val1Shadowed', $object->getProp1Shadowed(), 'Getting a shadowed property must work');
        $this->assertSame('val2Shadowed', $object->getProp2Shadowed(), 'Getting another shadowed property must work');
    }

    public function testSerializeAndUnserialize()
    {
        $object = new UnitTestPropertyAccessMainClass();

        // create accessors
        $prop1         = ScopedPropertyAccess::create(UnitTestPropertyAccessMainClass::class, 'prop1');
        $prop1Shadowed = ScopedPropertyAccess::create(UnitTestPropertyAccessBaseClass::class, 'prop1');

        // serialize and unserialize the accessors
        /** @var ScopedPropertyAccess $prop1Revived */
        /** @noinspection UnserializeExploitsInspection */
        $prop1Revived = unserialize(serialize($prop1));
        /** @var ScopedPropertyAccess $prop1ShadowedRevived */
        /** @noinspection UnserializeExploitsInspection */
        $prop1ShadowedRevived = unserialize(serialize($prop1Shadowed));

        // assert that the revived instances will get values correctly
        $object->setProp1('prop1');
        $object->setProp1Shadowed('prop1Shadowed');

        $this->assertSame('prop1', $prop1Revived->get($object), 'Getting must work after reviving');
        $this->assertSame('prop1Shadowed', $prop1ShadowedRevived->get($object), 'Getting shadowed property must work of reviving');

        // assert that the revived instances will get values correctly
        $prop1Revived->set($object, 'set1');
        $prop1ShadowedRevived->set($object, 'set1Shadowed');

        $this->assertSame('set1', $object->getProp1(), 'Setting must work after reviving');
        $this->assertSame('set1Shadowed', $object->getProp1Shadowed(), 'Setting shadowed property must work after reviving');
    }

    public function testWakeUp()
    {
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // force the init call on wakeup - we need to reset the static property "initialized"
        $initializedProp = (new \ReflectionClass(ScopedPropertyAccess::class))->getProperty('initialized');
        $initializedProp->setAccessible(true);
        $initializedProp->setValue(null, false);

        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // unserialized an accessor for UnitTestScopedPropertyAccessMainClass::prop1 and try to use it
        /** @noinspection SpellCheckingInspection */
        $serialized = "O:57:\"PeekAndPoke\Component\PropertyAccess\ScopedPropertyAccess\":2:{s:69:\"\000PeekAndPoke\Component\PropertyAccess\ScopedPropertyAccess\000scopeClass\";s:74:\"PeekAndPoke\Component\PropertyAccess\Stubs\UnitTestPropertyAccessMainClass\";s:71:\"\000PeekAndPoke\Component\PropertyAccess\ScopedPropertyAccess\000propertyName\";s:5:\"prop1\";}";

        /** @var ScopedPropertyAccess $subject */
        /** @noinspection UnserializeExploitsInspection */
        $subject = unserialize($serialized);

        $object = new UnitTestPropertyAccessMainClass();
        $subject->set($object, 'a');

        $this->assertSame('a', $object->getProp1(), 'Unserialized scoped property access must work');
    }
}
