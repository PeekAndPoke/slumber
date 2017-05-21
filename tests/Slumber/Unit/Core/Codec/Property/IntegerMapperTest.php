<?php
/**
 * Created by gerk on 18.05.17 23:45
 */

namespace PeekAndPoke\Component\Slumber\Unit\Core\Codec\Property;

use Doctrine\Common\Annotations\AnnotationReader;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsInteger;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericAwaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericSlumberer;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\IntegerMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Mocks\UnitTestServiceProvider;
use PHPUnit\Framework\TestCase;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class IntegerMapperTest extends TestCase
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
        $options = new AsInteger([]);
        $subject = new IntegerMapper($options);

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
        $options = new AsInteger([]);
        $subject = new IntegerMapper($options);

        self::assertSame($expected, $subject->slumber($this->slumberer, $input), 'slumber() must work');
    }

    public function provideTestSlumber()
    {
        $obj = new \DateTime();

        return [
            [null, 0],
            [0, 0],
            [1, 1],
            [1.1, 1],
            ['a', 0],
            ['0', 0],
            ['1', 1],
            ['1.2', 1],
            ['1a', 1],
            [[], 0],
            [[1, 2], 0],
            [$obj, 0],
            [[1, $obj], 0],
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
        $options = new AsInteger([]);
        $subject = new IntegerMapper($options);

        self::assertSame($expected, $subject->awake($this->awaker, $input), 'slumber() must work');
    }

    public function provideTestAwake()
    {
        $obj = new \DateTime();

        return [
            [null, 0],
            [0, 0],
            [1, 1],
            [1.1, 1],
            ['a', 0],
            ['0', 0],
            ['1', 1],
            ['1.2', 1],
            ['1a', 1],
            [[], 0],
            [[1, 2], 0],
            [$obj, 0],
            [[1, $obj], 0],
        ];
    }
}
