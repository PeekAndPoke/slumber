<?php
/**
 * Created by gerk on 21.02.18 10:41
 */

namespace PeekAndPoke\Component\Slumber\Unit\Core\Codec\Property\GeoJson;

use Doctrine\Common\Annotations\AnnotationReader;
use PeekAndPoke\Component\GeoJson\LineString;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\GeoJson\AsLineString;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericAwaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericSlumberer;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\GeoJson\LineStringMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\StaticServiceProvider;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class LineStringMapperTest extends TestCase
{
    /** @var Slumberer */
    protected $slumberer;
    /** @var Awaker */
    protected $awaker;

    public function setUp()
    {
        $lookUp = new AnnotatedEntityConfigReader(
            new StaticServiceProvider(),
            new AnnotationReader(),
            new ArrayCodecPropertyMarker2Mapper()
        );

        $this->slumberer = new GenericSlumberer($lookUp);
        $this->awaker    = new GenericAwaker($lookUp);
    }

    public function testConstruction()
    {
        $options = new AsLineString([]);
        $subject = new LineStringMapper($options);

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
        $options = new AsLineString([]);
        $subject = new LineStringMapper($options);

        self::assertSame($expected, $subject->slumber($this->slumberer, $input), 'slumber() must work');
    }

    public function provideTestSlumber()
    {
        return [
            // invalid input
            [null, null],
            [1, null],
            ['a', null],
            [new \DateTime(), null],
            [new \stdClass(), null],
            [[1, 2], null],

            // valid input
            [
                LineString::fromLngLats([]),
                [
                    'type' => LineString::TYPE,
                    'coordinates' => [],
                ],
            ],
            [
                LineString::fromLngLats([1.1, 2.2, 3.3, 4.4]),
                [
                    'type' => LineString::TYPE,
                    'coordinates' => [1.1, 2.2, 3.3, 4.4],
                ],
            ],
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
        $options = new AsLineString([]);
        $subject = new LineStringMapper($options);

        self::assertEquals($expected, $subject->awake($this->awaker, $input), 'awake() must work');
    }

    public function provideTestAwake()
    {
        return [
            // invalid
            [null, null],
            [0, null],
            [1, null],
            ['a', null],
            [[1, 2], null],
            [['type' => 'something'], null],
            [['coordinates' => [10.1, 20.2]], null],
            [['type' => 'something', 'coordinates' => 'no-array'], null],
            [['type' => 'something', 'coordinates' => [10.1, 20.2]], null],

            // valid
            [
                ['type' => LineString::TYPE, 'coordinates' => [10.1]],
                LineString::fromLngLats([10.1])
            ],
            [
                ['type' => LineString::TYPE, 'coordinates' => [10.1, 20.2]],
                LineString::fromLngLats([10.1, 20.2])
            ],
            [
                ['type' => LineString::TYPE, 'coordinates' => [10.1, 20.2, 30.3]],
                LineString::fromLngLats([10.1, 20.2, 30.3])
            ],
            [
                ['type' => LineString::TYPE, 'coordinates' => [10.1, 20.2, 30.3, 40.4]],
                LineString::fromLngLats([10.1, 20.2, 30.3, 40.4])
            ],
        ];
    }
}
