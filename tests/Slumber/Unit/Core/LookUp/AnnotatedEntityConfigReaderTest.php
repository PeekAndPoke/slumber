<?php
/**
 * Created by gerk on 24.02.18 09:30
 */

namespace PeekAndPoke\Component\Slumber\Unit\Core\LookUp;

use Doctrine\Common\Annotations\AnnotationReader;
use PeekAndPoke\Component\PropertyAccess\PublicPropertyAccess;
use PeekAndPoke\Component\PropertyAccess\ReflectionPropertyAccess;
use PeekAndPoke\Component\PropertyAccess\ScopedPropertyAccess;
use PeekAndPoke\Component\Slumber\Annotation\Slumber;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\DecimalMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\IntegerMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\StringMapper;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Core\LookUp\PropertyMarkedForSlumber;
use PeekAndPoke\Component\Slumber\StaticServiceProvider;
use PHPUnit\Framework\TestCase;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AnnotatedEntityConfigReaderTest extends TestCase
{
    /** @var AnnotatedEntityConfigReader */
    private $subject;

    public function setUp()
    {
        $this->subject = new AnnotatedEntityConfigReader(
            new StaticServiceProvider(),
            new AnnotationReader(),
            new ArrayCodecPropertyMarker2Mapper()
        );
    }

    public function testIt()
    {
        $config = $this->subject->getEntityConfig(new \ReflectionClass(TestClass::class));

        $this->assertEquals(7, $config->getNumMarkedProperties(), 'The number of marked properties must be correct');
    }

    /**
     * @param string                        $propertyName
     * @param PropertyMarkedForSlumber|null $expected
     *
     * @dataProvider provideTestForSpecificProperty
     */
    public function testForSpecificProperty($propertyName, $expected)
    {
        $config = $this->subject->getEntityConfig(new \ReflectionClass(TestClass::class));

        $this->assertEquals(
            $expected, $config->getMarkedPropertyByName($propertyName), 'The config of a marked property must be read correctly'
        );
    }

    /**
     * @return array
     */
    public function provideTestForSpecificProperty()
    {
        return [
            ['notMarked', null],
            ['NOT EXISTING', null],
            // props on the main class
            [
                'markedAsInt',
                PropertyMarkedForSlumber::create(
                    'markedAsInt',
                    'markedAsInt',
                    $asInteger = new Slumber\AsInteger(),
                    [$asInteger,],
                    new IntegerMapper($asInteger),
                    PublicPropertyAccess::create('markedAsInt')
                ),
            ],
            [
                'markedAsString',
                PropertyMarkedForSlumber::create(
                    'markedAsString',
                    'markedAsString',
                    $asString = new Slumber\AsString(),
                    [$asString,],
                    new StringMapper($asString),
                    PublicPropertyAccess::create('markedAsString')
                ),
            ],
            [
                'markedAsStringWithAlias',
                PropertyMarkedForSlumber::create(
                    'markedAsStringWithAlias',
                    'str alias',
                    $asStringWithAlias = new Slumber\AsString(['alias' => 'str alias']),
                    [$asStringWithAlias,],
                    new StringMapper($asStringWithAlias),
                    PublicPropertyAccess::create('markedAsStringWithAlias')
                ),
            ],
            [
                'protectedMarkedAsDecimal',
                PropertyMarkedForSlumber::create(
                    'protectedMarkedAsDecimal',
                    'protectedMarkedAsDecimal',
                    $asDecimal = new Slumber\AsDecimal(),
                    [$asDecimal,],
                    new DecimalMapper($asDecimal),
                    ReflectionPropertyAccess::create(new \ReflectionClass(TestClass::class), 'protectedMarkedAsDecimal')
                ),
            ],
            // props on the base class
            [
                'privateMarkedAsStringOnBase',
                PropertyMarkedForSlumber::create(
                    'privateMarkedAsStringOnBase',
                    'privateMarkedAsStringOnBase',
                    $asString = new Slumber\AsString(),
                    [$asString,],
                    new StringMapper($asString),
                    ScopedPropertyAccess::create(TestClassBase::class, 'privateMarkedAsStringOnBase')
                ),
            ],
            [
                'protectedMarkedAsDecimalOnBase',
                PropertyMarkedForSlumber::create(
                    'protectedMarkedAsDecimalOnBase',
                    'protectedMarkedAsDecimalOnBase',
                    $asDecimal = new Slumber\AsDecimal(),
                    [$asDecimal,],
                    new DecimalMapper($asDecimal),
                    ReflectionPropertyAccess::create(new \ReflectionClass(TestClass::class), 'protectedMarkedAsDecimalOnBase')
                ),
            ],
            // props on the base class coming in via a trait
            [
                'privateMarkedAsIntOnBaseTrait',
                PropertyMarkedForSlumber::create(
                    'privateMarkedAsIntOnBaseTrait',
                    'privateMarkedAsIntOnBaseTrait',
                    $asString = new Slumber\AsInteger(),
                    [$asString,],
                    new IntegerMapper($asString),
                    ScopedPropertyAccess::create(TestClassBase::class, 'privateMarkedAsIntOnBaseTrait')
                ),
            ],
        ];
    }
}

/**
 * @internal For unit tests only
 */
class TestClassBase
{
    use TestClassBaseTrait;

    public $notMarkedOnBase;

    /**
     * @var float
     *
     * @Slumber\AsDecimal()
     */
    protected $protectedMarkedAsDecimalOnBase;

    /**
     * @var string|null
     *
     * @Slumber\AsString()
     */
    private $privateMarkedAsStringOnBase;

    /**
     * @return null|string
     */
    public function getPrivateMarkedAsStringOnBase()
    {
        return $this->privateMarkedAsStringOnBase;
    }

    /**
     * @param null|string $privateMarkedAsStringOnBase
     *
     * @return $this
     */
    public function setPrivateMarkedAsStringOnBase($privateMarkedAsStringOnBase)
    {
        $this->privateMarkedAsStringOnBase = $privateMarkedAsStringOnBase;

        return $this;
    }
}

trait TestClassBaseTrait
{
    /**
     * @var int
     *
     * @Slumber\AsInteger()
     */
    private $privateMarkedAsIntOnBaseTrait;

    /**
     * @return int
     */
    public function getPrivateMarkedAsIntOnBaseTrait() : int
    {
        return $this->privateMarkedAsIntOnBaseTrait;
    }

    /**
     * @param int $privateMarkedAsIntOnBaseTrait
     *
     * @return $this
     */
    public function setPrivateMarkedAsIntOnBaseTrait(int $privateMarkedAsIntOnBaseTrait)
    {
        $this->privateMarkedAsIntOnBaseTrait = $privateMarkedAsIntOnBaseTrait;

        return $this;
    }
}

/**
 * @internal For unit tests only
 */
class TestClass extends TestClassBase
{
    public $notMarked = 10;

    /**
     * @var int
     *
     * @Slumber\AsInteger()
     */
    public $markedAsInt;

    /**
     * @var string
     *
     * @Slumber\AsString()
     */
    public $markedAsString;

    /**
     * @var string
     *
     * @Slumber\AsString(alias="str alias")
     */
    public $markedAsStringWithAlias;

    /**
     * @var float
     *
     * @Slumber\AsDecimal()
     */
    protected $protectedMarkedAsDecimal;
}
