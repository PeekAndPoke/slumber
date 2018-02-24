<?php
/**
 * Created by gerk on 17.02.18 10:42
 */

namespace PeekAndPoke\Component\PropertyAccess\Unit;

use PeekAndPoke\Component\PropertyAccess\ClassMirror;
use PeekAndPoke\Component\PropertyAccess\PublicPropertyAccess;
use PeekAndPoke\Component\PropertyAccess\ReflectionPropertyAccess;
use PeekAndPoke\Component\PropertyAccess\ScopedPropertyAccess;
use PeekAndPoke\Component\PropertyAccess\Stubs\UnitTestPropertyAccessMainClass;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ClassMirrorTest extends TestCase
{
    /**
     * @param string $propertyName
     * @param string $accessorClass
     *
     * @dataProvider provideTestGetAccessor
     */
    public function testGetAccessor($propertyName, $accessorClass)
    {
        $subject = new ClassMirror();

        $result = $subject->getAccessors(new UnitTestPropertyAccessMainClass());

        $this->assertSame(7, \count($result), 'getAccessors() must return the correct number of accessors');

        $this->assertInstanceOf($accessorClass, $result[$propertyName]);
    }

    public function provideTestGetAccessor()
    {
        return [
            ['prop1', ReflectionPropertyAccess::class],
            ['prop2', ReflectionPropertyAccess::class],
            ['publicProp', PublicPropertyAccess::class],
            ['protectedProp', ReflectionPropertyAccess::class],
            ['publicPropOnBase', PublicPropertyAccess::class],
            ['protectedPropOnBase', ReflectionPropertyAccess::class],
            ['privatePropOnBase', ScopedPropertyAccess::class],
        ];
    }
}
