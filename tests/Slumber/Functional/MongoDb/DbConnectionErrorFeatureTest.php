<?php
/**
 * Created by gerk on 03.12.17 16:23
 */

namespace PeekAndPoke\Component\Slumber\Functional\MongoDb;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\ArrayCache;
use MongoDB;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Data\EntityPoolImpl;
use PeekAndPoke\Component\Slumber\Data\EntityRepository;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbCodecSet;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbCursor;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbEntityConfigReaderCached;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbEntityConfigReaderImpl;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbPropertyMarkerToMapper;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbStorageDriver;
use PeekAndPoke\Component\Slumber\Data\StorageImpl;
use PeekAndPoke\Component\Slumber\Mocks\UnitTestServiceProvider;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestMainClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class DbConnectionErrorFeatureTest extends TestCase
{
    const DB_NAME               = 'slumber_tests_db';
    const MAIN_COLLECTION       = 'main_class';
    const REFERENCED_COLLECTION = 'ref_class';

    /** @var StorageImpl */
    static protected $storage;
    /** @var MongoDB\Client */
    static protected $client;
    /** @var EntityRepository */
    static protected $mainRepo;

    public static function setUpBeforeClass()
    {
        // setup the annotation reader for autoload
        AnnotationRegistry::registerLoader(function ($class) { return class_exists($class) || interface_exists($class) || trait_exists($class); });

        $di               = new UnitTestServiceProvider();
        $annotationReader = new AnnotationReader();
        $entityPool       = new EntityPoolImpl();

        self::$storage = new StorageImpl($entityPool);
        self::$client  = new MongoDB\Client(self::getDatabaseDns(), ['connect' => false]);

        $database           = self::$client->selectDatabase(self::DB_NAME);
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
                $database->selectCollection(self::MAIN_COLLECTION),
                new \ReflectionClass(UnitTestMainClass::class)
            )
        );
        self::$storage->addRepository(self::$mainRepo);
    }

    protected static function getDatabaseDns()
    {
        return 'mongodb://localhost:9999';
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
