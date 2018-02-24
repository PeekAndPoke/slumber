<?php
/**
 * Created by gerk on 21.02.18 10:41
 */

namespace PeekAndPoke\Component\Slumber\Unit\Core\Codec\Property\GeoJson;

use Doctrine\Common\Annotations\AnnotationReader;
use PeekAndPoke\Component\GeoJson\Point;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\GeoJson\AsPoint;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericAwaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericSlumberer;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\GeoJson\PointMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\StaticServiceProvider;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class PointMapperTest extends TestCase
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
        $options = new AsPoint([]);
        $subject = new PointMapper($options);

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
        $options = new AsPoint([]);
        $subject = new PointMapper($options);

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
                Point::fromLngLat( 23.24, 12.13),
                [
                    'type' => Point::TYPE,
                    'coordinates' => [23.24, 12.13],
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
        $options = new AsPoint([]);
        $subject = new PointMapper($options);

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
            [['type' => Point::TYPE, 'coordinates' => [10.1]], null],
            [['type' => Point::TYPE, 'coordinates' => [10.1, 20.2, 30.3]], null],

            // valid
            [
                ['type' => Point::TYPE, 'coordinates' => [10.1, 20.2]],
                Point::fromLngLat( 10.1, 20.2),
            ],
            [
                ['type' => Point::TYPE, 'coordinates' => [20.2, 10.1]],
                Point::fromLngLat( 20.2, 10.1),
            ],
        ];
    }
}
