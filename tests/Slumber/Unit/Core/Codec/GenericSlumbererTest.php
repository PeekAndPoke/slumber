<?php
/**
 * Created by gerk on 17.11.17 06:53
 */

namespace PeekAndPoke\Component\Slumber\Unit\Core\Codec;

use Doctrine\Common\Annotations\AnnotationReader;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\Codec\GenericSlumberer;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Core\LookUp\EntityConfigReader;
use PeekAndPoke\Component\Slumber\StaticServiceProvider;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestSlumberMainClass;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class GenericSlumbererTest extends TestCase
{
    /** @var GenericSlumberer */
    private $subject;

    public function setUp()
    {
        $di               = new StaticServiceProvider();
        $annotationReader = new AnnotationReader();
        $reader           = new AnnotatedEntityConfigReader($di, $annotationReader, new ArrayCodecPropertyMarker2Mapper());

        $this->subject = new GenericSlumberer($reader);
    }

    /**
     * For more detailed test on this have a look at SimplePersistenceFeatureTest.
     *
     * This one tests all kinds of types
     *
     * @see          SimplePersistenceFeatureTest
     *
     * @param          $data
     * @param callable $assert
     *
     * @dataProvider provideTestSlumberObjectExtraction
     */
    public function testSlumberObjectExtraction($data, callable $assert)
    {
        $result = $this->subject->slumber($data);

        $assert($result);
    }

    public function provideTestSlumberObjectExtraction()
    {

        return [
            [
                null,
                function ($data) {
                    $this->assertNull($data, 'slumber() must work');
                },
            ],
            [
                'null',
                function ($data) {
                    $this->assertNull($data, 'slumber() must work');
                },
            ],
            [
                ['a', 'b'],
                function ($data) {
                    $this->assertNull($data, 'slumber() must work');
                },
            ],
            [
                (new UnitTestSlumberMainClass())
                    ->setABool(true)
                    ->setAString('str'),
                function ($data) {
                    $this->assertTrue($data['aBool'], 'slumber() must work');
                    $this->assertSame('str', $data['aString'], 'slumber() must work');
                },
            ],
            [
                (new UnitTestSlumberMainClass())
                    ->setABool(false)
                    ->setAString('str2'),
                function ($data) {
                    $this->assertFalse($data['aBool'], 'slumber() must work');
                    $this->assertSame('str2', $data['aString'], 'slumber() must work');
                },
            ],
        ];
    }

    public function testSlumberForUnknownEntityConfig()
    {
        // we need to create a mock in order to return null for the entity config

        /** @var EntityConfigReader|\PHPUnit_Framework_MockObject_MockObject $configReaderMock */
        $configReaderMock = $this->getMockBuilder(EntityConfigReader::class)
            ->setMethods(['getEntityConfig'])
            ->getMock();

        $configReaderMock->method('getEntityConfig')->willReturn(null);

        $subject = new GenericSlumberer($configReaderMock);
        $result  = $subject->slumber(new \stdClass());

        $this->assertNull($result, 'Slumbering a class with no config available must return null');
    }
}
