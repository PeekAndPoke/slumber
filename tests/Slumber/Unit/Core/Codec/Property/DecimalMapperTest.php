<?php
/**
 * Created by gerk on 18.05.17 23:45
 */

namespace PeekAndPoke\Component\Slumber\Unit\Core\Codec\Property;

use Doctrine\Common\Annotations\AnnotationReader;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsDecimal;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericAwaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericSlumberer;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\DecimalMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Mocks\UnitTestServiceProvider;
use PHPUnit\Framework\TestCase;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class DecimalMapperTest extends TestCase
{
    /** @var Slumberer */
    protected $slumberer;
    /** @var Awaker */
    protected $awaker;

    public function setUp() : void
    {
        $lookUp = new AnnotatedEntityConfigReader(
            new UnitTestServiceProvider(),
            new AnnotationReader(),
            new ArrayCodecPropertyMarker2Mapper()
        );

        $this->slumberer = new GenericSlumberer($lookUp);
        $this->awaker    = new GenericAwaker($lookUp);
    }

    public function testConstruction() : void
    {
        $options = new AsDecimal([]);
        $subject = new DecimalMapper($options);

        self::assertSame($options, $subject->getOptions(), 'Construction must work');
    }

    /**
     * @param $input
     * @param $expected
     *
     * @dataProvider provideTestSlumber
     */
    public function testSlumber($input, $expected) : void
    {
        $options = new AsDecimal([]);
        $subject = new DecimalMapper($options);

        self::assertSame($expected, $subject->slumber($this->slumberer, $input), 'slumber() must work');
    }

    public function provideTestSlumber() : array
    {
        $obj = new \DateTime();

        return [
            [null, 0.0],
            [0, 0.0],
            [1, 1.0],
            ['a', 0.0],
            ['0', 0.0],
            ['1', 1.0],
            ['1.2', 1.2],
            ['1a', 1.0],
            [[], 0.0],
            [[1, 2], 0.0],
            [$obj, 0.0],
            [[1, $obj], 0.0],
        ];
    }

    /**
     * @param $input
     * @param $expected
     *
     * @dataProvider provideTestAwake
     */
    public function testAwake($input, $expected) : void
    {
        $options = new AsDecimal([]);
        $subject = new DecimalMapper($options);

        self::assertSame($expected, $subject->awake($this->awaker, $input), 'slumber() must work');
    }

    public function provideTestAwake() : array
    {
        $obj = new \DateTime();

        return [
            [null, 0.0],
            [0, 0.0],
            [1, 1.0],
            ['a', 0.0],
            ['0', 0.0],
            ['1', 1.0],
            ['1.2', 1.2],
            ['1a', 1.0],
            [[], 0.0],
            [[1, 2], 0.0],
            [$obj, 0.0],
            [[1, $obj], 0.0],
        ];
    }
}
