<?php
/**
 * Created by gerk on 18.05.17 23:45
 */

namespace PeekAndPoke\Component\Slumber\Unit\Core\Codec\Property;

use Doctrine\Common\Annotations\AnnotationReader;
use PeekAndPoke\Component\Slumber\Annotation\Slumber;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsObject;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericAwaker;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericSlumberer;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\ObjectMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Mocks\UnitTestServiceProvider;
use PeekAndPoke\Types\ValueHolder;
use PHPUnit\Framework\TestCase;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ObjectMapperTest extends TestCase
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
        $options = new AsObject(['value' => xxxObjectMapperTestSubjectXxx::class]);
        $subject = new ObjectMapper($options);

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
        $options = new AsObject(['value' => xxxObjectMapperTestSubjectXxx::class]);
        $subject = new ObjectMapper($options);

        self::assertSame($expected, $subject->slumber($this->slumberer, $input), 'slumber() must work');
    }

    public function provideTestSlumber()
    {
        $obj = new \DateTime();

        return [
            // default values
            [
                new xxxObjectMapperTestSubjectXxx(),
                [
                    'anInt' => 42,
                    'aString' => 'default',
                    'aListOfDecimals' => [0.1, 0.2],
                ],

            ],
            // other values
            [
                (new xxxObjectMapperTestSubjectXxx())->setAnInt(10)->setAString('str')->setAListOfDecimals([1.1, 2.2, 3.3]),
                [
                    'anInt' => 10,
                    'aString' => 'str',
                    'aListOfDecimals' => [1.1, 2.2, 3.3],
                ],
            ],
            // value holders
            [
                new xxxObjectMapperTestValueHolderXxx(
                    (new xxxObjectMapperTestSubjectXxx())->setAnInt(10)->setAString('str')->setAListOfDecimals([1.1, 2.2, 3.3])
                ),
                [
                    'anInt' => 10,
                    'aString' => 'str',
                    'aListOfDecimals' => [1.1, 2.2, 3.3],
                ],
            ],

            // things that map to null
            [null, null],
            [0, null],
            [1.1, null],
            ['a', null],
            [[], null],
            [[1, 2], null],
            [$obj, null],
            [[1, $obj], null],
            [new \stdClass, null]
        ];
    }

    /**
     * @param mixed                              $input
     * @param xxxObjectMapperTestSubjectXxx|null $expected
     *
     * @dataProvider provideTestAwake
     */
    public function testAwake($input, xxxObjectMapperTestSubjectXxx $expected = null)
    {
        $options = new AsObject(['value' => xxxObjectMapperTestSubjectXxx::class]);
        $subject = new ObjectMapper($options);

        /** @var xxxObjectMapperTestSubjectXxx $result */
        $result = $subject->awake($this->awaker, $input);

        if ($expected !== null) {
            self::assertInstanceOf(xxxObjectMapperTestSubjectXxx::class, $result, 'awake() must produce an object');
            self::assertSame($expected->getAnInt(), $result->getAnInt(), 'awake() must work');
            self::assertSame($expected->getAString(), $result->getAString(), 'awake() must work');
            self::assertSame($expected->getAListOfDecimals(), $result->getAListOfDecimals(), 'awake() must work');

        } else {

            self::assertNull($result, 'awake() must return null');
        }
    }

    public function provideTestAwake()
    {
        return [
            // things that create objects
            [
                [],
                new xxxObjectMapperTestSubjectXxx(),
            ],

            // things that map to null
            [null, null],
            [0, null],
            [1.1, null],
            ['a', null],
        ];
    }
}

class xxxObjectMapperTestValueHolderXxx implements ValueHolder
{
    /** @var xxxObjectMapperTestSubjectXxx */
    private $value;

    public function __construct(xxxObjectMapperTestSubjectXxx $value)
    {
        $this->value = $value;
    }

    /**
     * @return xxxObjectMapperTestSubjectXxx
     */
    public function getValue()
    {
        return $this->value;
    }
}

/**
 * @internal
 */
class xxxObjectMapperTestSubjectXxx
{
    /**
     * @var int
     *
     * @Slumber\AsInteger
     */
    private $anInt = 42;

    /**
     * @var string
     *
     * @Slumber\AsString
     */
    private $aString = 'default';

    /**
     * @var array
     *
     * @Slumber\AsList(
     *     @Slumber\AsDecimal()
     * )
     */
    private $aListOfDecimals = [0.1, 0.2];

    /**
     * @return int
     */
    public function getAnInt()
    {
        return $this->anInt;
    }

    /**
     * @param int $anInt
     *
     * @return $this
     */
    public function setAnInt($anInt)
    {
        $this->anInt = $anInt;

        return $this;
    }

    /**
     * @return string
     */
    public function getAString()
    {
        return $this->aString;
    }

    /**
     * @param string $aString
     *
     * @return $this
     */
    public function setAString($aString)
    {
        $this->aString = $aString;

        return $this;
    }

    /**
     * @return array
     */
    public function getAListOfDecimals()
    {
        return $this->aListOfDecimals;
    }

    /**
     * @param array $aListOfDecimals
     *
     * @return $this
     */
    public function setAListOfDecimals($aListOfDecimals)
    {
        $this->aListOfDecimals = $aListOfDecimals;

        return $this;
    }
}
