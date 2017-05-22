<?php
/**
 * Created by gerk on 18.05.17 23:45
 */

namespace PeekAndPoke\Component\Slumber\Unit\Core\Codec\Property;

use Doctrine\Common\Annotations\AnnotationReader;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsLocalDate;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericAwaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericSlumberer;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\LocalDateMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Mocks\UnitTestServiceProvider;
use PeekAndPoke\Types\LocalDate;
use PHPUnit\Framework\TestCase;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class LocalDateMapperTest extends TestCase
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
        $options = new AsLocalDate([]);
        $subject = new LocalDateMapper($options);

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
        $options = new AsLocalDate([]);
        $subject = new LocalDateMapper($options);

        self::assertSame($expected, $subject->slumber($this->slumberer, $input), 'slumber() must work');
    }

    public function provideTestSlumber()
    {
        $obj = new \DateTime();

        return [
            [
                new LocalDate('2017-01-01T00:00:00+00:00', 'Etc/UTC'),
                [
                    'date' => '2017-01-01T00:00:00+00:00',
                    'tz' => 'Etc/UTC',
                ],
            ],
            [
                new LocalDate('2017-01-01T00:00:00+00:00', 'Europe/Berlin'),
                [
                    'date' => '2017-01-01T01:00:00+01:00',
                    'tz' => 'Europe/Berlin',
                ],
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
        $options = new AsLocalDate([]);
        $subject = new LocalDateMapper($options);

        $result = $subject->awake($this->awaker, $input);

        if ($result instanceof LocalDate && $expected instanceof LocalDate) {
            self::assertSame($expected->getTimezone()->getName(), $result->getTimezone()->getName(), 'awake() must create the correct timezone');
            self::assertSame($expected->format(), $result->format(), 'awake() must create the correct date');

        } else {

            self::assertSame($expected, $subject->awake($this->awaker, $input), 'awake() must work');
        }
    }

    public function provideTestAwake()
    {
        $obj = new \DateTime();

        return [
            // TODO: things that should not work
            // ['a', null],

            // things that map to a local date
            [
                ['date' => '2017-02-03T12:00:00+00:00', 'tz' => 'Etc/UTC'],
                new LocalDate('2017-02-03T12:00:00+00:00', 'Etc/UTC'),
            ],
            [
                ['date' => '2017-02-03T13:00:00+01:00', 'tz' => 'Europe/Berlin'],
                new LocalDate('2017-02-03T13:00:00+01:00', 'Europe/Berlin'),
            ],
            [
                ['date' => '2017-02-03T12:00:00+00:00', 'tz' => 'Europe/Berlin'],
                new LocalDate('2017-02-03T13:00:00+01:00', 'Europe/Berlin'),
            ],
            // compat
            [
                '2017-02-03T12:00:00+00:00',
                new LocalDate('2017-02-03T12:00:00+00:00', '+00:00'),
            ],
            [
                '2017-02-03T12:00:00+01:00',
                new LocalDate('2017-02-03T12:00:00+01:00', '+01:00'),
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
