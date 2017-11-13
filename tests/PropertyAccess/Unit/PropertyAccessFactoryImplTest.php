<?php
/**
 * Created by gerk on 13.11.17 08:11
 */

namespace PeekAndPoke\Component\PropertyAccess\Unit;

use PeekAndPoke\Component\PropertyAccess\PropertyAccessFactory;
use PeekAndPoke\Component\PropertyAccess\PropertyAccessFactoryImpl;
use PeekAndPoke\Component\PropertyAccess\PublicPropertyAccess;
use PeekAndPoke\Component\PropertyAccess\ReflectionPropertyAccess;
use PeekAndPoke\Component\PropertyAccess\ScopedPropertyAccess;
use PeekAndPoke\Component\PropertyAccess\Stubs\UnitTestScopedPropertyAccessMainClass;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class PropertyAccessFactoryImplTest extends TestCase
{
    /** @var mixed */
    private $object;
    /** @var \ReflectionClass */
    private $class;
    /** @var  PropertyAccessFactory */
    private $factory;

    public function setUp()
    {
        $this->object  = new UnitTestScopedPropertyAccessMainClass();
        $this->class   = new \ReflectionClass($this->object);
        $this->factory = new PropertyAccessFactoryImpl();
    }

    /**
     * Public properties declared on the main class can be accessed with a PublicPropertyAccess
     */
    public function testForPublicPropertyOnMainClass()
    {
        $accessor = $this->factory->create($this->class, $this->class->getProperty('publicProp'));

        $this->assertInstanceOf(PublicPropertyAccess::class, $accessor, 'Public accessor on main class must be created correctly');
    }

    /**
     * Public properties declared on a base class can be accessed with a PublicPropertyAccess
     */
    public function testForPublicPropertyOnBaseClass()
    {
        $accessor = $this->factory->create($this->class, $this->class->getParentClass()->getProperty('publicPropOnBase'));

        $this->assertInstanceOf(PublicPropertyAccess::class, $accessor, 'Public accessor on base class must be created correctly');
    }

    /**
     * Protected properties declared on the main class can be accessed with a ReflectionPropertyAccess
     */
    public function testForProtectedPropertyOnMainClass()
    {
        $accessor = $this->factory->create($this->class, $this->class->getProperty('protectedProp'));

        $this->assertInstanceOf(ReflectionPropertyAccess::class, $accessor, 'Reflection accessor on main class must be created correctly');
    }

    /**
     * Protected properties declared on a base class can be accessed with a ReflectionPropertyAccess
     */
    public function testForProtectedPropertyOnBaseClass()
    {
        $accessor = $this->factory->create($this->class, $this->class->getParentClass()->getProperty('protectedPropOnBase'));

        $this->assertInstanceOf(ReflectionPropertyAccess::class, $accessor, 'Reflection accessor on base class must be created correctly');
    }

    /**
     * Private properties declared on the main class can be accessed with a ReflectionPropertyAccess ... yes ReflectionPropertyAccess !
     */
    public function testForPrivatePropertyOnMainClass()
    {
        $accessor = $this->factory->create($this->class, $this->class->getProperty('prop1'));

        $this->assertInstanceOf(ReflectionPropertyAccess::class, $accessor, 'Scoped accessor on main class must be created correctly');
    }

    /**
     * Private properties declared on a base class can ONLY be accessed with a ScopedPropertyAccess
     */
    public function testForPrivatePropertyOnBaseClass()
    {
        $accessor = $this->factory->create($this->class, $this->class->getParentClass()->getProperty('prop1'));

        $this->assertInstanceOf(ScopedPropertyAccess::class, $accessor, 'Reflection accessor on base class must be created correctly');
    }
}
