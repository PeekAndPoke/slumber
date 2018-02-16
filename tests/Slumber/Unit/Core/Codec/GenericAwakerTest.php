<?php
/**
 * Created by gerk on 17.11.17 06:05
 */

namespace PeekAndPoke\Component\Slumber\Unit\Core\Codec;

use Doctrine\Common\Annotations\AnnotationReader;
use PeekAndPoke\Component\Creator\NullCreator;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericAwaker;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Core\LookUp\EntityConfig;
use PeekAndPoke\Component\Slumber\Core\LookUp\EntityConfigReader;
use PeekAndPoke\Component\Slumber\StaticServiceProvider;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestSlumberMainClass;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestSlumberPolyChildA;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestSlumberPolyChildB;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestSlumberPolyChildC;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestSlumberPolyParent;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class GenericAwakerTest extends TestCase
{
    /** @var GenericAwaker */
    private $subject;

    public function setUp()
    {
        $di               = new StaticServiceProvider();
        $annotationReader = new AnnotationReader();

        $reader = new AnnotatedEntityConfigReader($di, $annotationReader, new ArrayCodecPropertyMarker2Mapper());

        $this->subject = new GenericAwaker($reader);
    }

    /**
     * @param array  $data
     * @param string $baseClass
     * @param string $expectedClass
     *
     * @dataProvider provideTestAwakePolymorphic
     */
    public function testAwakePolymorphic($data, $baseClass, $expectedClass)
    {
        /** @var mixed $result */
        $result = $this->subject->awake($data, new \ReflectionClass($baseClass));

        $this->assertSame(
            $expectedClass,
            \get_class($result),
            'Polymorphic awakening must work'
        );
    }

    public function provideTestAwakePolymorphic()
    {
        return [
            // not type at all ... must fall back to default
            [[], UnitTestSlumberPolyParent::class, UnitTestSlumberPolyChildC::class],
            // unknown type given ... must fall back to default
            [['type' => 'UNKNOWN'], UnitTestSlumberPolyParent::class, UnitTestSlumberPolyChildC::class],
            // valid type A ... must select correct poly
            [['type' => 'a'], UnitTestSlumberPolyParent::class, UnitTestSlumberPolyChildA::class],
            // valid type B ... must select correct poly
            [['type' => 'b'], UnitTestSlumberPolyParent::class, UnitTestSlumberPolyChildB::class],
        ];
    }

    /**
     * For more detailed test on this have a look at SimplePersistenceFeatureTest.
     *
     * This one tests all kinds of types
     *
     * @see SimplePersistenceFeatureTest
     *
     * @param          $data
     * @param callable $assert
     *
     * @dataProvider provideTestAwakeObjectPopulation
     */
    public function testAwakeObjectPopulation($data, callable $assert)
    {
        /** @var UnitTestSlumberMainClass $result */
        $result = $this->subject->awake($data, new \ReflectionClass(UnitTestSlumberMainClass::class));

        $assert($result);
    }

    public function provideTestAwakeObjectPopulation()
    {
        return [
            [
                ['aBool' => true, 'aString' => 'str', 'anInteger' => 10, 'aDecimal' => 10.1],
                function (UnitTestSlumberMainClass $result) {
                    $this->assertTrue($result->getABool(), 'Awaking must work');
                    $this->assertSame('str', $result->getAString(), 'Awaking must work');
                    $this->assertSame(10, $result->getAnInteger(), 'Awaking must work');
                    $this->assertSame(10.1, $result->getADecimal(), 'Awaking must work');
                }
            ],
            [
                ['aBool' => false, 'aString' => 'str2', 'anInteger' => 11.9, 'aDecimal' => 11.9],
                function (UnitTestSlumberMainClass $result) {
                    $this->assertFalse($result->getABool(), 'Awaking must work');
                    $this->assertSame('str2', $result->getAString(), 'Awaking must work');
                    $this->assertSame(11, $result->getAnInteger(), 'Awaking must work');
                    $this->assertSame(11.9, $result->getADecimal(), 'Awaking must work');
                }
            ],
        ];
    }

    public function testAwakeForUnknownEntityConfig()
    {
        // we need to create a mock in order to return null for the entity config

        /** @var EntityConfigReader|\PHPUnit_Framework_MockObject_MockObject $configReaderMock */
        $configReaderMock = $this->getMockBuilder(EntityConfigReader::class)
            ->setMethods(['getEntityConfig'])
            ->getMock();

        $configReaderMock->method('getEntityConfig')->willReturn(null);

        $subject = new GenericAwaker($configReaderMock);
        $result  = $subject->awake([], new \ReflectionClass(\stdClass::class));

        $this->assertNull($result, 'Awaking a class with no config available must return null');
    }

    public function testAwakeForCreatorReturningNull()
    {
        // we need to create a mock in order to return null for the entity config

        /** @var EntityConfigReader|\PHPUnit_Framework_MockObject_MockObject $configReaderMock */
        $configReaderMock = $this->getMockBuilder(EntityConfigReader::class)
            ->setMethods(['getEntityConfig'])
            ->getMock();

        $configReaderMock->method('getEntityConfig')->willReturn(
            new EntityConfig('n/a/', new NullCreator(), [], [])
        );

        $subject = new GenericAwaker($configReaderMock);
        $result  = $subject->awake([], new \ReflectionClass(\stdClass::class));

        $this->assertNull($result, 'Awaking a class with invalid creator must return null');
    }
}
