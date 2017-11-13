<?php
/**
 * Created by gerk on 13.11.17 22:04
 */

namespace PeekAndPoke\Component\Creator\Unit;

use PeekAndPoke\Component\Creator\CreateWithDefaultConstructor;
use PeekAndPoke\Component\Creator\CreateWithNonPublicConstructor;
use PeekAndPoke\Component\Creator\CreateWithoutConstructor;
use PeekAndPoke\Component\Creator\CreatorFactoryImpl;
use PeekAndPoke\Component\Creator\Stubs\UnitTestCreatableMandatoryCtorParams;
use PeekAndPoke\Component\Creator\Stubs\UnitTestCreatableWithoutCtor;
use PeekAndPoke\Component\Creator\Stubs\UnitTestCreatableWithParameterlessCtor;
use PeekAndPoke\Component\Creator\Stubs\UnitTestCreatableWithPrivateCtorAndOptionalParams;
use PeekAndPoke\Component\Creator\Stubs\UnitTestCreatableWithPublicCtorAndOptionalParams;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class CreatorFactoryImplTest extends TestCase
{
    public function testForClassWithoutConstructor()
    {
        $subject = new CreatorFactoryImpl();
        $result = $subject->create(new \ReflectionClass(UnitTestCreatableWithoutCtor::class));

        $this->assertInstanceOf(
            CreateWithDefaultConstructor::class,
            $result,
            'Factory must correctly create creator for classes without constructor'
        );
    }

    public function testForClassWithParameterlessConstructor()
    {
        $subject = new CreatorFactoryImpl();
        $result = $subject->create(new \ReflectionClass(UnitTestCreatableWithParameterlessCtor::class));

        $this->assertInstanceOf(
            CreateWithDefaultConstructor::class,
            $result,
            'Factory must correctly create creator for classes with public parameterless constructor'
        );
    }

    public function testForClassWithPublicConstructorWithOptionalParametersOnly()
    {
        $subject = new CreatorFactoryImpl();
        $result = $subject->create(new \ReflectionClass(UnitTestCreatableWithPublicCtorAndOptionalParams::class));

        $this->assertInstanceOf(
            CreateWithDefaultConstructor::class,
            $result,
            'Factory must correctly create creator for classes with public constructor with optional parameters'
        );
    }

    public function testForClassWithPrivateConstructorWithOptionalParametersOnly()
    {
        $subject = new CreatorFactoryImpl();
        $result = $subject->create(new \ReflectionClass(UnitTestCreatableWithPrivateCtorAndOptionalParams::class));

        $this->assertInstanceOf(
            CreateWithNonPublicConstructor::class,
            $result,
            'Factory must correctly create creator for classes with non-public constructor with optional parameters only'
        );
    }

    public function testForClassWithPublicConstructorWithMandatoryParams()
    {
        $subject = new CreatorFactoryImpl();
        $result = $subject->create(new \ReflectionClass(UnitTestCreatableMandatoryCtorParams::class));

        $this->assertInstanceOf(
            CreateWithoutConstructor::class,
            $result,
            'Factory must correctly create creator for classes with public constructor with mandatory parameters'
        );
    }
}
