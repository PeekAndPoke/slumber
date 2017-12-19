<?php
/**
 * Created by gerk on 03.12.17 16:23
 */

namespace PeekAndPoke\Component\Slumber\Functional\MongoDb;

use MongoDB;
use PeekAndPoke\Component\Slumber\Data\EntityRepository;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbCursor;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbStorageDriver;
use PeekAndPoke\Component\Slumber\Data\RepositoryRegistryImpl;
use PeekAndPoke\Component\Slumber\Data\StorageImpl;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestMainClass;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class DbConnectionErrorFeatureTest extends SlumberMongoDbTestBase
{
    const MAIN_COLLECTION = 'main_class';

    /** @var StorageImpl */
    static protected $storage;
    /** @var EntityRepository */
    static protected $mainRepo;

    public static function setUpBeforeClass()
    {
        $entityPool = static::createEntityPool();
        $registry   = new RepositoryRegistryImpl();

        self::$storage = new StorageImpl($entityPool, $registry);

        $codecSet = static::createCodecSet(self::$storage);

        $registry->registerProvider(self::MAIN_COLLECTION, [UnitTestMainClass::class], function () use ($entityPool, $codecSet) {

            $collection = static::createDatabase()->selectCollection(self::MAIN_COLLECTION);
            $reflect    = new \ReflectionClass(UnitTestMainClass::class);

            return new EntityRepository(self::MAIN_COLLECTION, new MongoDbStorageDriver($entityPool, $codecSet, $collection, $reflect));
        });

        // get the repos for use in the tests
        self::$mainRepo = self::$storage->getRepositoryByName(self::MAIN_COLLECTION);
    }

    protected static function createMongoClient()
    {
        return new MongoDB\Client('mongodb://localhost:9999', ['connect' => false]);
    }

    protected static function createDatabase()
    {
        return self::createMongoClient()->selectDatabase(self::DB_NAME);
    }

    /**
     * Tests that issuing a query will not try to connect to the database.
     *
     * It must only create a cursor.
     */
    public function testFindWithoutIteratingDoesNotThrow()
    {
        $result = self::$mainRepo->find();

        $this->assertInstanceOf(MongoDbCursor::class, $result);
    }

    /**
     * When trying to iterate the cursor an exception must be thrown.
     *
     * @expectedException     \PeekAndPoke\Component\Slumber\Data\MongoDb\Error\MongoDbConnectionError
     * @expectedExceptionCode 200
     */
    public function testFindGetFirstThrows()
    {
        $result = self::$mainRepo->find();
        $result->getFirst();
    }

    /**
     * When trying to count the cursor an exception must be thrown.
     *
     * @expectedException     \PeekAndPoke\Component\Slumber\Data\MongoDb\Error\MongoDbConnectionError
     * @expectedExceptionCode 200
     */
    public function testFindAndCountThrows()
    {
        $result = self::$mainRepo->find();
        $result->count();
    }

    /**
     * When trying to iterate with Psi an exception must be thrown.
     *
     * @expectedException     \PeekAndPoke\Component\Slumber\Data\MongoDb\Error\MongoDbConnectionError
     * @expectedExceptionCode 200
     */
    public function testFindAndPsiThrows()
    {
        $result = self::$mainRepo->find();
        $result->psi()->toArray();
    }

    /**
     * When trying to insert an exception must be thrown
     *
     * @expectedException     \PeekAndPoke\Component\Slumber\Data\MongoDb\Error\MongoDbConnectionError
     * @expectedExceptionCode 200
     */
    public function testInsertThrows()
    {
        self::$mainRepo->insert(new UnitTestMainClass());
    }

    /**
     * When trying to save an exception must be thrown
     *
     * @expectedException     \PeekAndPoke\Component\Slumber\Data\MongoDb\Error\MongoDbConnectionError
     * @expectedExceptionCode 200
     */
    public function testSaveThrows()
    {
        self::$mainRepo->save(new UnitTestMainClass());
    }

    /**
     * When trying to remove an exception must be thrown
     *
     * @expectedException     \PeekAndPoke\Component\Slumber\Data\MongoDb\Error\MongoDbConnectionError
     * @expectedExceptionCode 200
     */
    public function testRemoveThrows()
    {
        self::$mainRepo->remove(new UnitTestMainClass());
    }

    /**
     * When trying to remove all an exception must be thrown
     *
     * @expectedException     \PeekAndPoke\Component\Slumber\Data\MongoDb\Error\MongoDbConnectionError
     * @expectedExceptionCode 200
     */
    public function testRemoveAllThrows()
    {
        self::$mainRepo->removeAll([]);
    }
}
