<?php
/**
 * Created by gerk on 13.11.17 21:50
 */

namespace PeekAndPoke\Component\Creator\Unit;

use PeekAndPoke\Component\Creator\CreatePolymorphic;
use PeekAndPoke\Component\Creator\CreatorFactoryImpl;
use PeekAndPoke\Component\Creator\Stubs\UnitTestCreatablePolymorphicA;
use PeekAndPoke\Component\Creator\Stubs\UnitTestCreatablePolymorphicB;
use PeekAndPoke\Component\Creator\Stubs\UnitTestCreatablePolymorphicDefault;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class CreatePolymorphicTest extends TestCase
{
    /** @var CreatePolymorphic */
    private $subject;

    public function setUp()
    {
        $factory = new CreatorFactoryImpl();

        $this->subject = new CreatePolymorphic(
            [
                'a' => $factory->create(new \ReflectionClass(UnitTestCreatablePolymorphicA::class)),
                'b' => $factory->create(new \ReflectionClass(UnitTestCreatablePolymorphicB::class)),
            ],
            'type',
            $factory->create(new \ReflectionClass(UnitTestCreatablePolymorphicDefault::class))
        );
    }

    public function testWithNullData()
    {
        $result = $this->subject->create();

        $this->assertNull(
            $result,
            'Creator must create null when data is null'
        );
    }


    public function testEmptyArrayReturnsDefault()
    {
        $result = $this->subject->create([]);

        $this->assertInstanceOf(
            UnitTestCreatablePolymorphicDefault::class,
            $result,
            'Creator must create default class when discriminator is not present'
        );
    }

    public function testEmptyArrayObjectReturnsDefault()
    {
        $result = $this->subject->create(new \ArrayObject());

        $this->assertInstanceOf(
            UnitTestCreatablePolymorphicDefault::class,
            $result,
            'Creator must create default class when discriminator is not present'
        );
    }

    public function testDiscriminatorA()
    {
        $result = $this->subject->create(['type' => 'a']);

        $this->assertInstanceOf(
            UnitTestCreatablePolymorphicA::class,
            $result,
            'Creator must create object correctly by the discriminator'
        );
    }

    public function testDiscriminatorB()
    {
        $result = $this->subject->create(new \ArrayObject(['type' => 'b']));

        $this->assertInstanceOf(
            UnitTestCreatablePolymorphicB::class,
            $result,
            'Creator must create object correctly by the discriminator'
        );
    }
}
