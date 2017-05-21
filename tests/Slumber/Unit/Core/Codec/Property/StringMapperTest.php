<?php
/**
 * Created by gerk on 18.05.17 23:45
 */

namespace PeekAndPoke\Component\Slumber\Unit\Core\Codec\Property;

use Doctrine\Common\Annotations\AnnotationReader;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsString;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericAwaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericSlumberer;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\StringMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Mocks\UnitTestServiceProvider;
use PeekAndPoke\Types\LocalDate;
use PHPUnit\Framework\TestCase;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class StringMapperTest extends TestCase
{
    /** @var Slumberer */
    protected $slumberer;
    /** @var Awaker */
    protected $awaker;

    public function setUp()
    {
        $lookUp = new AnnotatedEntityConfigReader(
            new UnitTestServiceProvider(),
            new AnnotationReader(),
            new ArrayCodecPropertyMarker2Mapper()
        );

        $this->slumberer = new GenericSlumberer($lookUp);
        $this->awaker    = new GenericAwaker($lookUp);
    }

    public function testConstruction()
    {
        $options = new AsString([]);
        $subject = new StringMapper($options);

        self::assertSame($options, $subject->getOptions(), 'Construction must work');
    }

    /**
     * @param $input
     * @param $expected
     *
     * @dataProvider provideTestSlumber
     */
    public function testSlumber($input, $expected)
    {
        $options = new AsString([]);
        $subject = new StringMapper($options);

        self::assertSame($expected, $subject->slumber($this->slumberer, $input), 'slumber() must work');
    }

    public function provideTestSlumber()
    {
        $obj = new LocalDate('2017-01-01T12:00:00', 'Etc/UTC');

        return [
            [null, null],
            [0, '0'],
            [1, '1'],
            [1.1, '1.1'],
            ['a', 'a'],
            ['1.2', '1.2'],
            [[], null],
            [[1, 2], null],
            [$obj, $obj->format('c')],
            [new \stdClass(), null],
            [[1, $obj], null],
        ];
    }

    /**
     * @param $input
     * @param $expected
     *
     * @dataProvider provideTestAwake
     */
    public function testAwake($input, $expected)
    {
        $options = new AsString([]);
        $subject = new StringMapper($options);

        self::assertSame($expected, $subject->awake($this->awaker, $input), 'slumber() must work');
    }

    public function provideTestAwake()
    {
        $obj = new LocalDate('2017-01-01T12:00:00', 'Etc/UTC');

        return [
            [null, null],
            [0, '0'],
            [1, '1'],
            [1.1, '1.1'],
            ['a', 'a'],
            ['1.2', '1.2'],
            [[], null],
            [[1, 2], null],
            [$obj, $obj->format('c')],
            [new \stdClass(), null],
            [[1, $obj], null],
        ];
    }
}
