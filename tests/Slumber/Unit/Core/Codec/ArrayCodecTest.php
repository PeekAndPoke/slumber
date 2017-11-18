<?php
/**
 * Created by gerk on 17.11.17 15:03
 */

namespace PeekAndPoke\Component\Slumber\Unit\Core\Codec;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodec;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Mocks\UnitTestServiceProvider;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestPolyChildA;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ArrayCodecTest extends TestCase
{
    /** @var ArrayCodec */
    private $subject;

    public function setUp()
    {
        // setup the annotation reader for autoload
        AnnotationRegistry::registerLoader(
            function ($class) {
                return class_exists($class) || interface_exists($class) || trait_exists($class);
            }
        );

        $di               = new UnitTestServiceProvider();
        $annotationReader = new AnnotationReader();
        $reader           = new AnnotatedEntityConfigReader($di, $annotationReader, new ArrayCodecPropertyMarker2Mapper());

        $this->subject = new ArrayCodec($reader);
    }

    /**
     * @param mixed $input
     * @param mixed $expected
     *
     * @dataProvider provideTestSlumber()
     */
    public function testSlumber($input, $expected)
    {
        $result = $this->subject->slumber($input);

        if (is_scalar($input) || null === $input) {
            $this->assertSame($result, $expected, 'slumber() must work');
        } else {
            $this->assertEquals($result, $expected, 'slumber() must work');
        }
    }

    public function provideTestSlumber()
    {
        $obj = (new UnitTestPolyChildA())->setCommon('common')->setPropOnA('prop');
        $objResult = [
            'type'    => 'a',
            'common'  => 'common',
            'propOnA' => 'prop',
        ];

        return [
            // plain values
            [null, null],
            [1, 1],
            ['a', 'a'],

            // unknown objects
            [new \stdClass(), []],

            // arrays of plain objects
            [[null], [null]],
            [[1], [1]],
            [[1, 'a'], [1, 'a']],
            [[1, [null, 1, 'a']], [1, [null, 1, 'a']]],

            // slumber objects
            [$obj, $objResult],
            [[1, $obj], [1, $objResult]],
            [[$obj, $obj], [$objResult, $objResult]],
            [[$obj, [1, $obj]], [$objResult, [1, $objResult]]],
            [[$obj, [$obj, $obj]], [$objResult, [$objResult, $objResult]]],
        ];
    }

    public function testAwakeWithDataAndReflectionClass()
    {
        $data = [
            'type'    => 'a',
            'common'  => 'common',
            'propOnA' => 'prop',
        ];

        /** @var UnitTestPolyChildA $result */
        $result = $this->subject->awake($data, new \ReflectionClass(UnitTestPolyChildA::class));

        $this->assertInstanceOf(UnitTestPolyChildA::class, $result, 'awake() must create correct result');
        $this->assertSame('a', $result->getType(), 'awake() must populate result correctly');
        $this->assertSame('common', $result->getCommon(), 'awake() must populate result correctly');
        $this->assertSame('prop', $result->getPropOnA(), 'awake() must populate result correctly');
    }

    public function testAwakeWithDataAndFqcn()
    {
        $data = [
            'type'    => 'a',
            'common'  => 'common',
            'propOnA' => 'prop',
        ];

        /** @var UnitTestPolyChildA $result */
        $result = $this->subject->awake($data, UnitTestPolyChildA::class);

        $this->assertInstanceOf(UnitTestPolyChildA::class, $result, 'awake() must create correct result');
        $this->assertSame('a', $result->getType(), 'awake() must populate result correctly');
        $this->assertSame('common', $result->getCommon(), 'awake() must populate result correctly');
        $this->assertSame('prop', $result->getPropOnA(), 'awake() must populate result correctly');
    }

    /**
     * @param mixed $input
     *
     * @dataProvider provideTestAwakeWithInvalidDataReturnsNull
     */
    public function testAwakeWithInvalidDataReturnsNull($input)
    {
        $this->assertNull($this->subject->awake($input, UnitTestPolyChildA::class), 'awake() must return null on invalid data');
    }

    public function provideTestAwakeWithInvalidDataReturnsNull()
    {
        return [
            [null],
            [1],
            ['a'],
            [new \stdClass()],
        ];
    }

    public function testAwakeList()
    {
        $data = [
            'type'    => 'a',
            'common'  => 'common',
            'propOnA' => 'prop',
        ];

        /** @var UnitTestPolyChildA[] $result */
        $result = $this->subject->awakeList([$data, $data], UnitTestPolyChildA::class);

        $this->assertCount(2, $result, 'awakeList() must return array of correct size');

        $result1 = $result[0];
        $this->assertInstanceOf(UnitTestPolyChildA::class, $result1, 'awakeList() must create correct result');
        $this->assertSame('a', $result1->getType(), 'awakeList() must populate result correctly');
        $this->assertSame('common', $result1->getCommon(), 'awakeList() must populate result correctly');
        $this->assertSame('prop', $result1->getPropOnA(), 'awakeList() must populate result correctly');

        $result2 = $result[1];
        $this->assertInstanceOf(UnitTestPolyChildA::class, $result2, 'awakeList() must create correct result');
        $this->assertSame('a', $result2->getType(), 'awakeList() must populate result correctly');
        $this->assertSame('common', $result2->getCommon(), 'awakeList() must populate result correctly');
        $this->assertSame('prop', $result2->getPropOnA(), 'awakeList() must populate result correctly');
    }

    public function testAwakeList2()
    {
        $data = [
            'type'    => 'a',
            'common'  => 'common',
            'propOnA' => 'prop',
        ];

        /** @var UnitTestPolyChildA[] $result */
        $result = $this->subject->awakeList([$data, null], UnitTestPolyChildA::class);

        $this->assertCount(2, $result, 'awakeList() must return array of correct size');

        $result1 = $result[0];
        $this->assertInstanceOf(UnitTestPolyChildA::class, $result1, 'awakeList() must create correct result');
        $this->assertSame('a', $result1->getType(), 'awakeList() must populate result correctly');
        $this->assertSame('common', $result1->getCommon(), 'awakeList() must populate result correctly');
        $this->assertSame('prop', $result1->getPropOnA(), 'awakeList() must populate result correctly');

        $result2 = $result[1];
        $this->assertNull($result2, 'awakeList() must create correct result');
    }

    /**
     * @param mixed $input
     *
     * @dataProvider provideTestAwakeListWithInvalidDataReturnsEmptyArray
     */
    public function testAwakeListWithInvalidDataReturnsEmptyArray($input)
    {
        $this->assertSame(
            [],
            $this->subject->awakeList($input, UnitTestPolyChildA::class),
            'awakeList() must return null on invalid data'
        );
    }

    public function provideTestAwakeListWithInvalidDataReturnsEmptyArray()
    {
        return [
            [null],
            [1],
            ['a'],
            [new \stdClass()],
        ];
    }

}