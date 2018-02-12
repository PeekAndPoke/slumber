<?php
/**
 * Created by gerk on 12.02.18 09:23
 */

namespace PeekAndPoke\Component\Slumber\Unit\Data\MongoDb\Types;

use Doctrine\Common\Annotations\AnnotationReader;
use MongoDB\BSON\UTCDateTime;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsSimpleDate;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericAwaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericSlumberer;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Data\MongoDb\Types\SimpleDateMapper;
use PeekAndPoke\Component\Slumber\Helper\UnitTestServiceProvider;
use PeekAndPoke\Types\LocalDate;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class SimpleDateMapperTest extends TestCase
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
        $options = new AsSimpleDate([]);
        $subject = new SimpleDateMapper($options);

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
        $options = new AsSimpleDate([]);
        $subject = new SimpleDateMapper($options);

        $result = $subject->slumber($this->slumberer, $input);

        self::assertEquals($expected, $result, 'slumber() must work correctly');
    }

    public function provideTestSlumber()
    {
        $obj = new \DateTime();

        return [
            [
                new \DateTime('2017-01-01T12:00:00+00:00', new \DateTimeZone('Etc/UTC')),
                new UTCDateTime(1483272000000)
            ],
            [
                new \DateTime('2017-01-01T12:00:00.100000+00:00', new \DateTimeZone('Etc/UTC')),
                new UTCDateTime(1483272000100)
            ],
            [
                new \DateTime('2017-01-01T12:00:00.120000+00:00', new \DateTimeZone('Etc/UTC')),
                new UTCDateTime(1483272000120)
            ],
            [
                new \DateTime('2017-01-01T12:00:00.123000+00:00', new \DateTimeZone('Etc/UTC')),
                new UTCDateTime(1483272000123)
            ],
            [
                new \DateTime('2017-01-01T12:00:00.123400+00:00', new \DateTimeZone('Etc/UTC')),
                new UTCDateTime(1483272000123)
            ],
            [
                new \DateTime('2017-01-01T12:00:00.123450+00:00', new \DateTimeZone('Etc/UTC')),
                new UTCDateTime(1483272000123)
            ],
            [
                new \DateTime('2017-01-01T12:00:00.123456+00:00', new \DateTimeZone('Etc/UTC')),
                new UTCDateTime(1483272000123)
            ],
            [
                new \DateTime('2017-01-01T12:00:00.123999+00:00', new \DateTimeZone('Etc/UTC')),
                new UTCDateTime(1483272000123)
            ],

            [
                new LocalDate('2017-01-01T12:00:00+00:00', 'Europe/Berlin'),
                new UTCDateTime(1483272000000)
            ],
            [
                new LocalDate('2017-01-01T12:00:00.100000+00:00', 'Europe/Berlin'),
                new UTCDateTime(1483272000100)
            ],
            [
                new LocalDate('2017-01-01T12:00:00.123999+00:00', 'Europe/Berlin'),
                new UTCDateTime(1483272000123)
            ],
            // things that map to null
            [null, null],
            [0, null],
            ['a', null],
            [[1, 2], null],
            [[1, $obj], null]
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
        $options = new AsSimpleDate([]);
        $subject = new SimpleDateMapper($options);

        $result = $subject->awake($this->awaker, $input);

        self::assertEquals($expected, $result, 'awake() must work');
    }

    public function provideTestAwake()
    {
        $obj = new \DateTime();

        return [
            // TODO: things that should not work
            // ['a', null],

            [
                new \DateTime('2017-02-03T12:00:00+00:00'),
                new \DateTime('2017-02-03T12:00:00+00:00'),
            ],
            // compatibility
            [
                ['date' => '2017-02-03T12:00:00+00:00', 'tz' => 'Etc/Utc'],
                new \DateTime('2017-02-03T12:00:00+00:00', new \DateTimeZone('Etc/Utc')),
            ],
            [
                ['date' => '2017-02-03T13:00:00+01:00', 'tz' => 'Europe/Berlin'],
                new \DateTime('2017-02-03T13:00:00+01:00', new \DateTimeZone('Europe/Berlin')),
            ],

            // things that map to null
            [
                ['date' => 'abc', 'tz' => 'Europe/Berlin'],
                null,
            ],
            [
                ['date' => '2017-01-01T00:00:00', 'tz' => 'UNKNOWN'],
                null,
            ],
            [null, null],
            [0, null],
            [[1, 2], null],
            [[1, $obj], null]
        ];
    }

}
