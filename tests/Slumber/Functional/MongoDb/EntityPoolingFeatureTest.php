<?php
/**
 * Created by gerk on 13.11.17 17:04
 */

namespace PeekAndPoke\Component\Slumber\Functional\MongoDb;

use PeekAndPoke\Component\Slumber\Data\EntityPool;
use PeekAndPoke\Component\Slumber\Data\EntityPoolImpl;
use PeekAndPoke\Component\Slumber\Data\EntityRepository;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbStorageDriver;
use PeekAndPoke\Component\Slumber\Data\RepositoryRegistryImpl;
use PeekAndPoke\Component\Slumber\Data\StorageImpl;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestAggregatedClass;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestMainClass;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
final class EntityPoolingFeatureTest extends SlumberMongoDbTestBase
{
    const MAIN_COLLECTION       = 'main_class';
    const REFERENCED_COLLECTION = 'ref_class';

    /** @var StorageImpl */
    static protected $storage;
    /** @var EntityRepository */
    static protected $mainRepo;
    /** @var EntityRepository */
    static protected $referencedRepo;

    public static function setUpBeforeClass()
    {
        $entityPool = EntityPoolImpl::getInstance();
        $registry   = new RepositoryRegistryImpl();

        self::$storage = new StorageImpl($entityPool, $registry);

        $codecSet = static::createCodecSet(self::$storage);

        $registry->registerProvider(self::MAIN_COLLECTION, [UnitTestMainClass::class], function () use ($entityPool, $codecSet) {

            $collection = static::createDatabase()->selectCollection(self::MAIN_COLLECTION);
            $reflect    = new \ReflectionClass(UnitTestMainClass::class);

            return new EntityRepository(self::MAIN_COLLECTION, new MongoDbStorageDriver($entityPool, $codecSet, $collection, $reflect));
        });

        $registry->registerProvider(self::REFERENCED_COLLECTION, [UnitTestAggregatedClass::class], function () use ($entityPool, $codecSet) {

            $collection = static::createDatabase()->selectCollection(self::REFERENCED_COLLECTION);
            $reflect    = new \ReflectionClass(UnitTestAggregatedClass::class);

            return new EntityRepository(self::REFERENCED_COLLECTION, new MongoDbStorageDriver($entityPool, $codecSet, $collection, $reflect));
        });

        // get the repos for use in the tests
        self::$mainRepo       = self::$storage->getRepositoryByName(self::MAIN_COLLECTION);
        self::$referencedRepo = self::$storage->getRepositoryByName(self::REFERENCED_COLLECTION);
    }

    public function testGettingObjectByIdTwiceGivesTheSameObject()
    {
        $subject = new UnitTestMainClass();
        $subject->setId('TEST001');

        self::$mainRepo->save($subject);

        $isInPool = self::$storage->getEntityPool()->has(new \ReflectionClass($subject), EntityPool::PRIMARY_ID, 'TEST001');

        $this->assertTrue($isInPool, 'After saving the entity must be in the pool');

        // load by id
        $reloaded = self::$mainRepo->findById('TEST001');

        $this->assertSame($subject, $reloaded, 'The reloaded entity must come from the pool and must be the same instance as the saved one');
    }


    public function testSavingANewObjectIsAddedToThePool()
    {
        // setup
        $subject = new UnitTestMainClass();
        $subject->setId('TEST001');

        self::$mainRepo->save($subject);

        // clear the pool
        self::$storage->getEntityPool()->clear();

        $this->assertCount(0, self::$storage->getEntityPool()->all(), 'The entity pool must be empty');

        // load by id
        /** @var UnitTestMainClass $reloaded */
        $reloaded = self::$mainRepo->findById('TEST001');

        $this->assertNotSame($subject, $reloaded, 'The entity must NOT come from the pool, since the pool must be empty');
        $this->assertSame($subject->getId(), $reloaded->getId(), 'Reloading must work');

        /** @var UnitTestMainClass $reloaded */
        $reloadedAgain = self::$mainRepo->findById('TEST001');

        $this->assertSame($reloaded, $reloadedAgain, 'Reloading a second time must return the entity from the pool');
    }
}
