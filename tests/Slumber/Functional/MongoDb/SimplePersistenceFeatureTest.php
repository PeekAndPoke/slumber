<?php
/**
 * File was created 12.10.2015 06:31
 */

namespace PeekAndPoke\Component\Slumber\Functional\MongoDb;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\ArrayCache;
use MongoDB;
use PeekAndPoke\Component\GeoJson\Point;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Data\EntityPoolImpl;
use PeekAndPoke\Component\Slumber\Data\EntityRepository;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbCodecSet;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbEntityConfigReaderCached;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbEntityConfigReaderImpl;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbPropertyMarkerToMapper;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbStorageDriver;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbUtil;
use PeekAndPoke\Component\Slumber\Data\StorageImpl;
use PeekAndPoke\Component\Slumber\Mocks\UnitTestServiceProvider;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestAggregatedClass;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestCollection;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestMainClass;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestOtherClass;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestPolyChildA;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestPolyChildB;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestPolyChildC;
use PeekAndPoke\Types\LocalDate;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class SimplePersistenceFeatureTest extends TestCase
{
    /** @var StorageImpl */
    static protected $storage;
    /** @var MongoDB\Client */
    static protected $client;
    /** @var EntityRepository */
    static protected $mainRepo;
    /** @var EntityRepository */
    static protected $refRepo;

    public static function setUpBeforeClass()
    {
        // setup the annotation reader for autoload
        AnnotationRegistry::registerLoader(
            function ($class) {
                return class_exists($class) || interface_exists($class) || trait_exists($class);
            }
        );

        $di               = new UnitTestServiceProvider();
        $annotationReader = new AnnotationReader();
        $entityPool       = new EntityPoolImpl();

        self::$storage = new StorageImpl($entityPool);
        self::$client  = new MongoDB\Client('mongodb://localhost:27017', ['connect' => false]);

        $database           = self::$client->selectDatabase('slumber_tests_db');
        $entityConfigReader = new MongoDbEntityConfigReaderCached(
            new MongoDbEntityConfigReaderImpl(
                new AnnotatedEntityConfigReader($di, $annotationReader, new MongoDbPropertyMarkerToMapper())
            ),
            new ArrayCache(),
            'test',
            true
        );
        $codecSet           = new MongoDbCodecSet($di, $entityConfigReader, self::$storage, new NullLogger());

        self::$mainRepo = new EntityRepository(
            'main_class',
            new MongoDbStorageDriver(
                $entityPool,
                $codecSet,
                $database->selectCollection('main_class'),
                new \ReflectionClass(UnitTestMainClass::class)
            )
        );
        self::$storage->addRepository(self::$mainRepo);

        self::$refRepo = new EntityRepository(
            'ref_class',
            new MongoDbStorageDriver(
                $entityPool,
                $codecSet,
                $database->selectCollection('ref_class'),
                new \ReflectionClass(UnitTestAggregatedClass::class)
            )
        );
        self::$storage->addRepository(self::$refRepo);
    }

    public function setUp()
    {
        // clear the repo before every test
        self::$mainRepo->removeAll();
        self::$refRepo->removeAll();
    }

    /**
     * Save and reload an item
     */
    public function testSaveAndReloadWorks()
    {
        $item = static::createPopulatedItem();

        self::$mainRepo->save($item);

        // test fields getting populated
        static::assertNotEmpty($item->getId(), 'The "id" must be populated after saving');
        static::assertTrue(MongoDbUtil::isValidMongoIdString($item->getId()), 'The "id" must be a valid MongoId');
        static::assertNotEmpty($item->getReference(), 'The "reference" must be populated after saving');

        // test the saved object is now in the pool
        $pooled = self::$mainRepo->findById($item->getId());

        static::assertSame(
            $item,
            $pooled,
            'Reloading the item with findById() must return the exact same object an no another instance'
        );

        // there must be one item in the repo
        $result = self::$mainRepo->find();

        static::assertCount(1, $result, 'There must be one document in the repository');
        static::assertSame(
            $result->getFirst(),
            $item,
            'Reloading the item with find() must return the exact same object an no another instance'
        );

        // clearing the pool and reloading must result in another object
        self::$storage->getEntityPool()->clear();
        /** @var UnitTestMainClass $reloaded */
        $reloaded = self::$mainRepo->findById($item->getId());

        static::assertNotSame($item, $reloaded, 'After clearing the pool we must get a new instance of the object');
    }

    public function testReloadingWorksWithSelfSetId()
    {
        $testId = 'TEST-ID';

        $item = static::createPopulatedItem();
        $item->setId($testId);

        self::$mainRepo->save($item);
        self::$storage->getEntityPool()->clear();
        /** @var UnitTestMainClass $reloaded */
        $reloaded = self::$mainRepo->findById($item->getId());

        static::assertNotSame($item, $reloaded);
        static::assertEquals($testId, $reloaded->getId());
    }

    public function testBehavioursWorksOnSave()
    {
        $original = static::createPopulatedItem();

        self::$mainRepo->save($original);
        self::$storage->getEntityPool()->clear();

        /** @var UnitTestMainClass $reloaded */
        $reloaded = self::$mainRepo->findById($original->getId());

        static::assertStringStartsWith(
            'PeekAndPoke\Component\Slumber\Stubs\UnitTestMainClass@',
            $reloaded->getReference(),
            'The reference must be populated'
        );

        static::assertTrue(
            (new LocalDate('now -5 seconds', 'Etc/UTC'))->isBefore($reloaded->getCreatedAt()),
            'The creation date must be set correctly'
        );
        static::assertTrue(
            (new LocalDate('now +5 seconds', 'Etc/UTC'))->isAfter($reloaded->getCreatedAt()),
            'The creation date must be set correctly'
        );

        static::assertTrue(
            (new LocalDate('now -5 seconds', 'Etc/UTC'))->isBefore($reloaded->getUpdatedAt()),
            'The update date must be set correctly'
        );
        static::assertTrue(
            (new LocalDate('now +5 seconds', 'Etc/UTC'))->isAfter($reloaded->getUpdatedAt()),
            'The update date must be set correctly'
        );

        // save it again and check if only the things changed that should change
        usleep(1100000); // sleep enough so we can be sure that the updatedAt has changed

        self::$mainRepo->save($reloaded);
        self::$storage->getEntityPool()->clear();

        static::assertCount(1, self::$mainRepo->find(), 'There must only be one entry in the collection');

        /** @var UnitTestMainClass $again */
        $again = self::$mainRepo->findById($original->getId());

        static::assertNotSame($reloaded, $again);
        static::assertSame(
            $reloaded->getReference(),
            $again->getReference(),
            'The reference must only change when it is empty'
        );

        static::assertEquals(
            $reloaded->getCreatedAt()->getTimestamp(),
            $again->getCreatedAt()->getTimestamp(),
            'The creation date must NOT be changed'
        );
        static::assertNotEquals(
            $original->getUpdatedAt()->getTimestamp(),
            $again->getUpdatedAt()->getTimestamp(),
            'The update date must be changed'
        );
        static::assertTrue(
            $original->getUpdatedAt()->getTimestamp() < $again->getUpdatedAt()->getTimestamp(),
            'The update date must be changed'
        );
    }

    public function testReloadedItemContainsAListOfPolymorphics()
    {
        $original = static::createPopulatedItem();

        // save the main as the last one
        self::$storage->save($original);
        self::$storage->getEntityPool()->clear();

        /** @var UnitTestMainClass $reloadedMain */
        $reloadedMain = self::$mainRepo->findById($original->getId());

        $polyList = $reloadedMain->getAListOfPolymorphics();

        static::assertCount(4, $polyList, 'The right number of Polymorphics must be reloaded');

        /** @var UnitTestPolyChildA $polyOne */
        $polyOne = $polyList[0];

        static::assertInstanceOf(
            UnitTestPolyChildA::class,
            $polyOne,
            'Polymorphic list-items must be reloaded correctly'
        );
        static::assertEquals('myA', $polyOne->getPropOnA(), 'Polymorphic properties must be set correctly');
        static::assertEquals(
            'commonA',
            $polyOne->getCommon(),
            'Common properties of Polymorphic must be set correctly'
        );

        /** @var UnitTestPolyChildB $polyTwo */
        $polyTwo = $polyList[1];

        static::assertInstanceOf(
            UnitTestPolyChildB::class,
            $polyTwo,
            'Polymorphic list-items must be reloaded correctly'
        );
        static::assertEquals('myB', $polyTwo->getPropOnB(), 'Polymorphic properties must be set correctly');
        static::assertEquals(
            'commonB',
            $polyTwo->getCommon(),
            'Common properties of Polymorphic must be set correctly'
        );

        /** @var UnitTestPolyChildC $polyDefaultType */
        $polyDefaultType = $polyList[2];

        // test the default type of the polymorphic
        static::assertInstanceOf(
            UnitTestPolyChildC::class,
            $polyDefaultType,
            'Polymorphic list-items of the default type must be reloaded correctly'
        );
        static::assertEquals('myC', $polyDefaultType->getPropOnC(), 'Polymorphic properties must be set correctly');
        static::assertEquals(
            'commonC',
            $polyDefaultType->getCommon(),
            'Common properties of Polymorphic must be set correctly'
        );

        /** @var UnitTestPolyChildA $polyFour */
        $polyFour = $polyList[3];

        static::assertInstanceOf(
            UnitTestPolyChildA::class,
            $polyFour,
            'Polymorphic list-items must be reloaded correctly'
        );
        static::assertEquals('myA2', $polyFour->getPropOnA(), 'Polymorphic properties must be set correctly');
        static::assertEquals(
            'commonA2',
            $polyFour->getCommon(),
            'Common properties of Polymorphic must be set correctly'
        );
    }

    /**
     * Test persisting and reloading basic types
     */
    public function testReloadedItemContainsReferencedObject()
    {
        $item = static::createPopulatedItem();

        // save the referenced object individually
        self::$storage->save($item->getAReferencedObject());
        // save the main as the last so that the referenced has an id already
        self::$storage->save($item);

        self::$storage->getEntityPool()->clear();
        /** @var UnitTestMainClass $reloadedMain */
        $reloadedMain = self::$mainRepo->findById($item->getId());

        static::assertNotNull($reloadedMain->getAReferencedObject(), 'A referenced object must be reloaded');
        static::assertNotEmpty(
            $reloadedMain->getAReferencedObject()->getReference(),
            'A referenced object must have its reference set'
        );
        static::assertEquals(
            'ref',
            $reloadedMain->getAReferencedObject()->getName(),
            'A referenced object must be reloaded correctly'
        );

        self::$storage->getEntityPool()->clear();
        /** @var UnitTestAggregatedClass $reloadedRef */
        $reloadedRef = self::$refRepo->findByReference($reloadedMain->getAReferencedObject()->getReference());

        static::assertNotNull($reloadedRef, 'A referenced object must be reloaded directly');
        static::assertEquals('ref', $reloadedRef->getName(), 'A referenced object must be reloaded correctly');
    }

    /**
     * Test persisting and reloading basic types
     */
    public function testReloadedItemContainsAListOfReferencedObjects()
    {
        $item = static::createPopulatedItem();

        self::$mainRepo->save($item);
        self::$storage->getEntityPool()->clear();
        /** @var UnitTestMainClass $reloadedMain */
        $reloadedMain = self::$mainRepo->findById($item->getId());

        static::assertNotNull(
            $reloadedMain->getAListOfReferencedObjects(),
            'A list of referenced objects must be reloaded'
        );

        $referencedObjects = $reloadedMain->getAListOfReferencedObjects();

        static::assertCount(
            3,
            $reloadedMain->getAListOfReferencedObjects(),
            'The count of reloaded referenced objects must be correct'
        );
        static::assertEquals(
            'child_01',
            $referencedObjects[0]->getName(),
            'A referenced object must be reloaded correctly'
        );
        static::assertEquals(
            'child_02',
            $referencedObjects[1]->getName(),
            'A referenced object must be reloaded correctly'
        );
        static::assertEquals(
            'child_03',
            $referencedObjects[2]->getName(),
            'A referenced object must be reloaded correctly'
        );

        // iterating must also work
        foreach ($referencedObjects as $referencedObject) {
            static::assertInstanceOf(
                UnitTestAggregatedClass::class,
                $referencedObject,
                'The referenced objects must be unwrapped from the LazyDbReference when iterating'
            );
        }
    }

    public function testReloadedItemContainsAListOfMapsOfReferencedObjects()
    {
        $item = static::createPopulatedItem();

        self::$mainRepo->save($item);
        self::$storage->getEntityPool()->clear();
        /** @var UnitTestMainClass $reloadedMain */
        $reloadedMain = self::$mainRepo->findById($item->getId());

        static::assertNotNull(
            $reloadedMain->getAListOfMapsOfReferencedObjects(),
            'A list of maps of referenced objects must be reloaded'
        );

        /** @var UnitTestAggregatedClass[][] $referencedObjects */
        $referencedObjects = $reloadedMain->getAListOfMapsOfReferencedObjects();

        // accessing the referenced object via index must work

        static::assertCount(
            2,
            $reloadedMain->getAListOfMapsOfReferencedObjects(),
            'The count of reloaded referenced objects must be correct'
        );
        static::assertEquals(
            'list_map_a',
            $referencedObjects[0]['a']->getName(),
            'A referenced object must be reloaded correctly'
        );
        static::assertEquals(
            'list_map_b',
            $referencedObjects[0]['b']->getName(),
            'A referenced object must be reloaded correctly'
        );
        static::assertEquals(
            'list_map_c',
            $referencedObjects[1]['c']->getName(),
            'A referenced object must be reloaded correctly'
        );
    }

    /**
     * Test persisting and reloading basic types
     */
    public function testReloadedItemContainsCorrectBasicTypeEntries()
    {
        $item = static::createPopulatedItem();

        self::$mainRepo->save($item);
        self::$storage->getEntityPool()->clear();
        /** @var UnitTestMainClass $reloaded */
        $reloaded = self::$mainRepo->findById($item->getId());

        static::assertNotSame($reloaded, $item, 'The reloaded entity must not be the same as the original one');
        static::assertNotNull($reloaded, 'Reloading by id must work');

        static::assertSame($item->getABool(), $reloaded->getABool(), 'Boolean must be stored correctly');
        static::assertTrue(is_bool($reloaded->getABool()), 'Boolean must be stored correctly');

        static::assertSame($item->getAnotherBool(), $reloaded->getAnotherBool(), 'Boolean must be stored correctly');
        static::assertTrue(is_bool($reloaded->getAnotherBool()), 'Boolean must be stored correctly');

        static::assertSame($item->getADecimal(), $reloaded->getADecimal(), 'Decimals must be stored correctly');
        static::assertTrue(is_float($reloaded->getADecimal()), 'Decimals must be stored correctly');

        static::assertSame($item->getAnInteger(), $reloaded->getAnInteger(), 'Integers must be stored correctly');
        static::assertTrue(is_int($reloaded->getAnInteger()), 'Integers must be stored correctly');

        static::assertSame($item->getAString(), $reloaded->getAString(), 'Strings must be stored correctly');
        static::assertTrue(is_string($reloaded->getAString()), 'Strings must be stored correctly');

        static::assertNull($reloaded->getAStringContainingNull(), 'It must be able to save null for a string');

        static::assertSame(
            $item->getASomethingAsIs(),
            $reloaded->getASomethingAsIs(),
            'Mixed things must be stored correctly'
        );
        static::assertTrue(is_string($reloaded->getASomethingAsIs()), 'SmallInts must be stored correctly');

        static::assertSame(
            $item->getASomethingElseAsIs(),
            $reloaded->getASomethingElseAsIs(),
            'Mixed things must be stored correctly'
        );
        static::assertTrue(is_int($reloaded->getASomethingElseAsIs()), 'SmallInts must be stored correctly');

        static::assertSame(
            $item->getASimpleDate()->getTimestamp(),
            $reloaded->getASimpleDate()->getTimestamp(),
            'Simple DateTime must be stored correctly'
        );

        static::assertSame(
            $item->getALocalDate()->getTimestamp(),
            $reloaded->getALocalDate()->getTimestamp(),
            'LocalDates must be stored correctly'
        );
        static::assertSame(
            $item->getALocalDate()->getTimezone()->getName(),
            $reloaded->getALocalDate()->getTimezone()->getName(),
            'LocalDates timezones must be stored correctly'
        );
        static::assertInstanceOf(LocalDate::class, $item->getALocalDate(), 'LocalDates must be stored correctly');
    }

    /**
     * Test persisting and reloading basic types
     */
    public function testReloadedItemContainsCorrectGeoJsonEntries()
    {
        $item = static::createPopulatedItem();

        self::$mainRepo->save($item);
        self::$storage->getEntityPool()->clear();

        static::assertCount(1, self::$mainRepo->find(), 'There must only be one entry in the collection');

        /** @var UnitTestMainClass $reloaded */
        $reloaded = self::$mainRepo->findById($item->getId());

        static::assertNotNull($reloaded, 'Reloading by id must work');

        static::assertInstanceOf(
            Point::class,
            $reloaded->getAGeoJsonPoint(),
            'A GeoJson point must be reloaded correctly'
        );
        static::assertSame(
            $item->getAGeoJsonPoint()->getLat(),
            $reloaded->getAGeoJsonPoint()->getLat(),
            'The LAT of the reloaded GeoJson point must be correct'
        );
        static::assertSame(
            $item->getAGeoJsonPoint()->getLng(),
            $reloaded->getAGeoJsonPoint()->getLng(),
            'The LNG of the reloaded GeoJson point must be correct'
        );
    }

//    /**
//     * Test @Slumber\AsCollection(@Slumber\AsInteger())
//     */
//    public function testReloadedItemContainsCorrectCollectionOfIntegers()
//    {
//        $item = static::createPopulatedItem();
//
//        self::$mainRepo->save($item);
//        self::$storage->getEntityPool()->clear();
//        /** @var UnitTestMainClass $reloaded */
//        $reloaded = self::$mainRepo->findById($item->getId());
//
//        static::assertNotNull($reloaded, 'Reloading by id must work');
//
//        static::assertSame(
//            [
//                'a' => 1,
//                'b' => 2,
//                'c' => 1,
//                'd' => 0,
//                'e' => 0,
//                'f' => 0,
//                'g' => 0,
//                'h' => 0,
//            ],
//            $reloaded->getACollectionOfIntegers(),
//            'A slumbering array of integers must be reloaded correctly'
//        );
//    }

    /**
     * Test @Slumber\AsCollection(@Slumber\AsString())
     */
    public function testReloadedItemContainsCorrectCollectionOfStrings()
    {
        $item = static::createPopulatedItem();

        self::$mainRepo->save($item);
        self::$storage->getEntityPool()->clear();
        /** @var UnitTestMainClass $reloaded */
        $reloaded = self::$mainRepo->findById($item->getId());

        static::assertNotNull($reloaded, 'Reloading by id must work');

        static::assertSame(
            [
                'a' => '1',
                'b' => '2',
                'c' => '1',
                'd' => '',
                'e' => null,
                'f' => null,
                'g' => null,
                'h' => null,
            ],
            $reloaded->getAMapOfStrings(),
            'A slumbering array of integers must be reloaded correctly'
        );
    }
//
//    /**
//     * Test @Slumber\AsCollection(@Slumber\AsIs())
//     */
//    public function testReloadedItemContainsCorrectCollectionOfMixed()
//    {
//        $item = static::createPopulatedItem();
//
//        self::$mainRepo->save($item);
//        self::$storage->getEntityPool()->clear();
//        /** @var UnitTestMainClass $reloaded */
//        $reloaded = self::$mainRepo->findById($item->getId());
//
//        static::assertNotNull($reloaded, 'Reloading by id must work');
//
//        static::assertSame(
//            [
//                'a' => 1,
//                'b' => '2',
//                'c' => true,
//                'd' => false,
//                'e' => null,
//                'f' => [1, 2],
//                'g' => [],
//                'h' => [],
//            ],
//            $reloaded->getACollectionOfMixed(),
//            'A slumbering array of integers must be reloaded correctly'
//        );
//    }
//
//    /**
//     * Test persisting an array of Objects
//     *
//     * By default the @Slumber\AsObject markers has keepNullValuesInCollections flag set to false.
//     * Therefore we should only reload TWO valid objects in the collection.
//     *
//     * Test @Slumber\AsCollection(@Slumber\AsObject(UnitTestAggregatedClass::class))
//     */
//    public function testReloadedItemContainsCorrectCollectionOfObjects()
//    {
//        $item            = static::createPopulatedItem();
//        $originalObjects = $item->getACollectionOfObjects();
//
//        self::$mainRepo->save($item);
//        self::$storage->getEntityPool()->clear();
//        /** @var UnitTestMainClass $reloaded */
//        $reloaded = self::$mainRepo->findById($item->getId());
//
//        static::assertNotNull($reloaded, 'Reloading by id must work');
//        $reloadedObjects = $reloaded->getACollectionOfObjects();
//
//        static::assertCount(2, $reloadedObjects, 'There must be two valid object that have been stored');
//
//        static::assertEquals(
//            $originalObjects[0]->getName(),
//            $reloadedObjects[0]->getName(),
//            'The first object must be reloaded correctly'
//        );
//
//        static::assertEquals(
//            $originalObjects[1]->getName(),
//            $reloadedObjects[1]->getName(),
//            'The second object must be reloaded correctly'
//        );
//    }

    /**
     * Test that a nested List of List of integers is stored a reloaded correctly
     *
     * @throws \PeekAndPoke\Component\Slumber\Core\Exception\SlumberException
     */
    public function testReloadedItemContainsCorrectListOfListsOfIntegers()
    {
        $item = static::createPopulatedItem();

        self::$mainRepo->save($item);
        self::$storage->getEntityPool()->clear();
        /** @var UnitTestMainClass $reloaded */
        $reloaded = self::$mainRepo->findById($item->getId());

        static::assertNotNull($reloaded, 'Reloading by id must work');

        static::assertSame(
            [
                [1, 1, 1, 0, 0, 0, 0,],
                [2, 2, 1, 0, 0, 0, 0,],
            ],
            $reloaded->getAListOfListsOfIntegers(),
            'A slumbering list of lists of integers must be reloaded correctly'
        );
    }

    /**
     * Test that a nested Map of List of integers is stored a reloaded correctly
     *
     * @throws \PeekAndPoke\Component\Slumber\Core\Exception\SlumberException
     */
    public function testReloadedItemContainsCorrectMapOfListsOfIntegers()
    {
        $item = static::createPopulatedItem();

        self::$mainRepo->save($item);
        self::$storage->getEntityPool()->clear();
        /** @var UnitTestMainClass $reloaded */
        $reloaded = self::$mainRepo->findById($item->getId());

        static::assertNotNull($reloaded, 'Reloading by id must work');

        static::assertSame(
            [
                'a' => [1, 1, 1, 0, 0, 0, 0,],
                'b' => [2, 2, 1, 0, 0, 0, 0,],
            ],
            $reloaded->getAMapOfListsOfIntegers(),
            'A slumbering map of lists of integers must be reloaded correctly'
        );
    }

    /**
     * Test that a nested Map of Maps of integers is stored a reloaded correctly
     *
     * @throws \PeekAndPoke\Component\Slumber\Core\Exception\SlumberException
     */
    public function testReloadedItemContainsCorrectMapOfMapsOfIntegers()
    {
        $item = static::createPopulatedItem();

        self::$mainRepo->save($item);
        self::$storage->getEntityPool()->clear();
        /** @var UnitTestMainClass $reloaded */
        $reloaded = self::$mainRepo->findById($item->getId());

        static::assertNotNull($reloaded, 'Reloading by id must work');

        static::assertSame(
            [
                'a' => ['a1' => 1, 'a2' => 1, 'a3' => 1, 'a4' => 0, 'a5' => 0, 'a6' => 0, 'a7' => 0,],
                'b' => ['b1' => 2, 'b2' => 2, 'b3' => 1, 'b4' => 0, 'b5' => 0, 'b6' => 0, 'b7' => 0,],
            ],
            $reloaded->getAMapOfMapsOfIntegers(),
            'A slumbering map of lists of integers must be reloaded correctly'
        );
    }

    /**
     * Test that a nested List of List of strings is stored a reloaded correctly
     *
     * @throws \PeekAndPoke\Component\Slumber\Core\Exception\SlumberException
     */
    public function testReloadedItemContainsCorrectListOfListsOfStrings()
    {
        $item = static::createPopulatedItem();

        self::$mainRepo->save($item);
        self::$storage->getEntityPool()->clear();
        /** @var UnitTestMainClass $reloaded */
        $reloaded = self::$mainRepo->findById($item->getId());

        static::assertNotNull($reloaded, 'Reloading by id must work');

        static::assertSame(
            [
                ['1', '1', '1', '', null, null, null,],
                ['2', '2', '1', '', null, null, null,],
            ],
            $reloaded->getAListOfListsOfStrings(),
            'A slumbering list of lists of strings must be reloaded correctly'
        );
    }

    /**
     * Test that a nested List of List of mixed is stored a reloaded correctly
     *
     * @throws \PeekAndPoke\Component\Slumber\Core\Exception\SlumberException
     */
    public function testReloadedItemContainsCorrectListOfListsOfMixed()
    {
        $item = static::createPopulatedItem();

        self::$mainRepo->save($item);
        self::$storage->getEntityPool()->clear();
        /** @var UnitTestMainClass $reloaded */
        $reloaded = self::$mainRepo->findById($item->getId());

        static::assertNotNull($reloaded, 'Reloading by id must work');

        static::assertSame(
            [
                [1, '1', true, false, [1, 2], [], [],],
                [2, '2', true, false, [1, 2], [], [],],
            ],
            $reloaded->getAListOfListsOfMixed(),
            'A slumbering list of lists of mixed must be reloaded correctly'
        );
    }

    public function testReloadedItemContainsCorrectListOfStringWrappedInACollClass()
    {
        $item = static::createPopulatedItem();

        self::$mainRepo->save($item);
        self::$storage->getEntityPool()->clear();
        /** @var UnitTestMainClass $reloaded */
        $reloaded = self::$mainRepo->findById($item->getId());

        static::assertNotNull($reloaded, 'Reloading by id must work');

        static::assertInstanceOf(
            UnitTestCollection::class,
            $item->getAListOfStringWrappedInACollClass(),
            'A list wrapped in a collection class must be instantiated correctly'
        );

        static::assertSame(
            ['a', 'b'],
            $reloaded->getAListOfStringWrappedInACollClass()->getData(),
            'A List wrapped in a collection class must be reloaded correctly'
        );
    }

    /**
     * Test that a nested List of List of objects is stored a reloaded correctly
     *
     * @throws \PeekAndPoke\Component\Slumber\Core\Exception\SlumberException
     */
    public function testReloadedItemContainsCorrectListOfListsOfObjects()
    {
        $item = static::createPopulatedItem();

        self::$mainRepo->save($item);
        self::$storage->getEntityPool()->clear();
        /** @var UnitTestMainClass $reloaded */
        $reloaded = self::$mainRepo->findById($item->getId());

        static::assertNotNull($reloaded, 'Reloading by id must work');

        $firstList = $reloaded->getAListOfListsOfObjects()[0];
        static::assertCount(2, $firstList, 'There must be two valid object that have been stored');

        /** @noinspection PhpUndefinedMethodInspection */
        static::assertSame(
            $item->getAListOfListsOfObjects()['a']['a1']->getName(),
            $firstList[0]->getName(),
            '1st object in a list of lists of objects must be reloaded correctly'
        );
        /** @noinspection PhpUndefinedMethodInspection */
        static::assertSame(
            $item->getAListOfListsOfObjects()['a']['a2']->getName(),
            $firstList[1]->getName(),
            '2nd object in a list of lists of objects must be reloaded correctly'
        );

        $secondsList = $reloaded->getAListOfListsOfObjects()[1];
        static::assertCount(2, $secondsList, 'There must be two valid object that have been stored');

        /** @noinspection PhpUndefinedMethodInspection */
        static::assertSame(
            $item->getAListOfListsOfObjects()['b']['b1']->getName(),
            $secondsList[0]->getName(),
            '1st object in a list of lists of objects must be reloaded correctly'
        );
        /** @noinspection PhpUndefinedMethodInspection */
        static::assertSame(
            $item->getAListOfListsOfObjects()['b']['b2']->getName(),
            $secondsList[1]->getName(),
            '2nd object in a list of lists of objects must be reloaded correctly'
        );
    }

    /**
     * @return UnitTestMainClass
     */
    public static function createPopulatedItem()
    {
        $collectionInput = [
            'a' => 1,
            'b' => '2',
            'c' => true,
            'd' => false,
            'e' => null,        // the as @Slumber\AsIs() must keep this one
            'f' => [1, 2],
            'g' => [],
            'h' => new \stdClass(),
        ];

        $nestedInput = [
            'a' => [
                'a1' => 1,
                'a2' => '1',
                'a3' => true,
                'a4' => false,
                'a5' => [1, 2],
                'a6' => [],
                'a7' => new \stdClass()
            ],
            'b' => [
                'b1' => 2,
                'b2' => '2',
                'b3' => true,
                'b4' => false,
                'b5' => [1, 2],
                'b6' => [],
                'b7' => new \stdClass()
            ],
        ];

        $nestedInputObjects = [
            'a' => [
                'a1' => (new UnitTestAggregatedClass())->setName('Obj a1'),
                'a2' => (new UnitTestAggregatedClass())->setName('Obj a2'),
                'a3' => (new UnitTestOtherClass())->setOtherName('Other a1'),
                'a4' => true,
                'a5' => false,
                'a6' => null,
            ],
            'b' => [
                'b1' => (new UnitTestAggregatedClass())->setName('Obj b1'),
                'b2' => (new UnitTestAggregatedClass())->setName('Obj b2'),
                'b3' => (new UnitTestOtherClass())->setOtherName('Other b1'),
                'b4' => true,
                'b5' => false,
                'b6' => null,
            ],
        ];

        ////  List of referenced objects  ///////////////////////////////////////////////////////

        $aListOfReferencedObjects = [
            (new UnitTestAggregatedClass())->setName('child_01'),
            (new UnitTestAggregatedClass())->setName('child_02'),
            // test that NULLs are not kept in collections of referenced objects
            null,
            (new UnitTestAggregatedClass())->setName('child_03'),
            // test that NULLs are not kept in collections of referenced objects
            null,
        ];

        // We need to store the referenced entities individually. This is not done when storing the main object
        foreach ($aListOfReferencedObjects as $referencedObject) {
            if (static::$refRepo) {
                static::$refRepo->save($referencedObject);
            }
        }

        ////  List of Map of referenced objects  ///////////////////////////////////////////////////////

        $aListOfMapsOfReferencedObjects = [
            [
                'a' => (new UnitTestAggregatedClass())->setName('list_map_a'),
                'b' => (new UnitTestAggregatedClass())->setName('list_map_b'),
            ],
            [
                'c' => (new UnitTestAggregatedClass())->setName('list_map_c'),
                'd' => null,
            ],
        ];

        // We need to store the referenced entities individually. This is not done when storing the main object
        array_walk_recursive(
            $aListOfMapsOfReferencedObjects,
            function ($item) {
                if ($item !== null && static::$refRepo) {
                    static::$refRepo->save($item);
                }
            }
        );

        ////  Build the main object  ///////////////////////////////////////////////////////

        $item = new UnitTestMainClass();
        $item
            ->setAnObject((new UnitTestAggregatedClass())->setName('anObject'))// todo: test this
            ->setAListOfPolymorphics(
                [
                    (new UnitTestPolyChildA())->setPropOnA('myA')->setCommon('commonA'),
                    (new UnitTestPolyChildB())->setPropOnB('myB')->setCommon('commonB'),
                    (new UnitTestPolyChildC())->setPropOnC('myC')->setCommon('commonC'),
                    (new UnitTestPolyChildA())->setPropOnA('myA2')->setCommon('commonA2'),
                ]
            )
            // references to other collections
            ->setAReferencedObject((new UnitTestAggregatedClass())->setName('ref'))
            ->setAListOfReferencedObjects($aListOfReferencedObjects)
            ->setAListOfMapsOfReferencedObjects($aListOfMapsOfReferencedObjects)
            // collections
            ->setAMapOfIntegers($collectionInput)
            ->setAMapOfStrings($collectionInput)
            ->setAMapOfMixed($collectionInput)
            ->setAMapOfObjects(
                [
                    $objInCol1 = (new UnitTestAggregatedClass())->setName('Obj 1'),
                    $objInCol2 = (new UnitTestAggregatedClass())->setName('Obj 2'),
                    $otherObj = (new UnitTestOtherClass())->setOtherName('Other 1'),
                    true,
                    false,
                    null,
                ]
            )
            // nested list of lists, map of lists, map of maps
            ->setAListOfListsOfIntegers($nestedInput)
            ->setAMapOfListsOfIntegers($nestedInput)
            ->setAMapOfMapsOfIntegers($nestedInput)
            ->setAListOfStringWrappedInACollClass(
                new UnitTestCollection(['a', 'b'])
            )
            ->setAListOfListsOfMixed($nestedInput)
            ->setAListOfListsOfStrings($nestedInput)
            ->setAListOfListsOfObjects($nestedInputObjects)
            // bool
            ->setABool(true)
            ->setAnotherBool(false)
            // decimal
            ->setADecimal(123.456)
            // integer
            ->setAnInteger(234)
            // string
            ->setAString('Test')
            ->setAStringContainingNull(null)
            // built in types
            ->setASimpleDate(new \DateTime('2016-04-18'))
            ->setALocalDate(new LocalDate('2015-10-12', 'Etc/UTC'))
            // TODO: test the AsJsonString

            // GeoJson
            ->setAGeoJsonPoint(new Point(52.5, 13.3))
            // mapped as it is
            ->setASomethingAsIs('AsIs')
            ->setASomethingElseAsIs(987);

        return $item;
    }
}
