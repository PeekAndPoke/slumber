<?php
/**
 * Created by gerk on 18.05.17 23:45
 */

namespace PeekAndPoke\Component\Slumber\Unit\Core\Codec\Property;

use Doctrine\Common\Annotations\AnnotationReader;
use PeekAndPoke\Component\Collections\ArrayCollection;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsList;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsString;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericAwaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericSlumberer;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\ListMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\StringMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\StaticServiceProvider;
use PeekAndPoke\Types\LocalDate;
use PHPUnit\Framework\TestCase;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ListMapperTest extends TestCase
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
        $options = new AsList(['value' => new AsString([])]);
        $subject = new ListMapper($options, new StringMapper(new AsString([])));

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
        $options = new AsList(['value' => new AsString([])]);
        $subject = new ListMapper($options, new StringMapper(new AsString([])));

        self::assertSame($expected, $subject->slumber($this->slumberer, $input), 'slumber() must work');
    }

    public function provideTestSlumber()
    {
        $obj = new LocalDate('2017-01-01T12:00:00', 'Etc/UTC');

        return [
            [null, null],
            [0, null],
            [1, null],
            [1.1, null],
            ['a', null],
            ['1.2', null],
            [[], []],
            [[1, 2], ['1', '2']],
            [['a' => 1, 'b' => 2], ['1', '2']],
            [[1, null, 2], ['1', null, '2']],
            [new ArrayCollection([1, 2]), ['1', '2']],
            [$obj, null],
            [new \stdClass(), null],
            [[1, $obj], ['1', $obj->format()]],
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
        $options = new AsList(['value' => new AsString([])]);
        $subject = new ListMapper($options, new StringMapper(new AsString([])));

        self::assertSame($expected, $subject->awake($this->awaker, $input), 'slumber() must work');
    }

    /**
     * @param $input
     * @param $expected
     *
     * @dataProvider provideTestAwake
     */
    public function testAwakeIntoCollection($input, $expected)
    {
        $options = new AsList(['value' => new AsString([]), 'collection' => ArrayCollection::class]);
        $subject = new ListMapper($options, new StringMapper(new AsString([])));

        /** @var ArrayCollection $result */
        $result = $subject->awake($this->awaker, $input);

        self::assertInstanceOf(ArrayCollection::class, $result, 'awake() must produce an ArrayCollection');
        self::assertSame($expected, $result->getData(), 'awake() must work');
    }

    // TODO: test with keepNullsInCollection = false

    public function provideTestAwake()
    {
        return [
            [null, []],
            [0, []],
            [1, []],
            [1.1, []],
            ['a', []],
            ['1.2', []],
            [[], []],
            [[1, 2], ['1', '2']],
            [[1, null, 2], ['1', null, '2']],
            [['a' => 1, 'b' => 2], ['1', '2']],
        ];
    }
}
