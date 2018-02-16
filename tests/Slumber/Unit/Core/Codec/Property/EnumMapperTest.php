<?php
/**
 * Created by gerk on 18.05.17 23:45
 */

namespace PeekAndPoke\Component\Slumber\Unit\Core\Codec\Property;

use Doctrine\Common\Annotations\AnnotationReader;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsEnum;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericAwaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericSlumberer;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\EnumMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\StaticServiceProvider;
use PeekAndPoke\Types\Enumerated;
use PHPUnit\Framework\TestCase;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class EnumMapperTest extends TestCase
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
        $options = new AsEnum(['value' => xxxEnumMapperTestEnumXxx::class]);
        $subject = new EnumMapper($options);

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
        $options = new AsEnum(['value' => xxxEnumMapperTestEnumXxx::class]);
        $subject = new EnumMapper($options);

        self::assertSame($expected, $subject->slumber($this->slumberer, $input), 'slumber() must work');
    }

    public function provideTestSlumber()
    {
        $obj = new \DateTime();

        return [
            [xxxEnumMapperTestEnumXxx::$ONE, xxxEnumMapperTestEnumXxx::$ONE->getValue()],
            [xxxEnumMapperTestEnumXxx::$TWO, xxxEnumMapperTestEnumXxx::$TWO->getValue()],
            [xxxEnumMapperTestEnumXxx::void(), ''],
            [xxxEnumMapperTestEnumXxx::from('UNKNOWN'), 'UNKNOWN'],
            [null, null],
            [0, null],
            [1, null],
            ['a', null],
            ['1a', null],
            [[], null],
            [[1, 2], null],
            [$obj, null],
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
        $options = new AsEnum(['value' => xxxEnumMapperTestEnumXxx::class]);
        $subject = new EnumMapper($options);

        self::assertSame($expected, $subject->awake($this->awaker, $input), 'slumber() must work');
    }

    public function provideTestAwake()
    {
        $obj = new \DateTime();

        return [
            [xxxEnumMapperTestEnumXxx::$ONE->getValue(), xxxEnumMapperTestEnumXxx::$ONE],
            [xxxEnumMapperTestEnumXxx::$TWO->getValue(), xxxEnumMapperTestEnumXxx::$TWO],
            [null, xxxEnumMapperTestEnumXxx::from(null)],
            [0, xxxEnumMapperTestEnumXxx::from(0)],
            [1, xxxEnumMapperTestEnumXxx::from(1)],
            ['a', xxxEnumMapperTestEnumXxx::from('a')],
            ['1a', xxxEnumMapperTestEnumXxx::from('1a')],
            [[], xxxEnumMapperTestEnumXxx::from([])],
            [[1, 2], xxxEnumMapperTestEnumXxx::from([1, 2])],
            [$obj, xxxEnumMapperTestEnumXxx::from($obj)],
        ];
    }
}

class xxxEnumMapperTestEnumXxx extends Enumerated
{
    /** @var xxxEnumMapperTestEnumXxx */
    public static $ONE;
    /** @var xxxEnumMapperTestEnumXxx */
    public static $TWO;
}

xxxEnumMapperTestEnumXxx::init();
