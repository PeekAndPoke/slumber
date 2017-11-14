<?php
/**
 * Created by gerk on 30.10.17 06:28
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
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbEntityConfigReaderCached;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbEntityConfigReaderImpl;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbPropertyMarkerToMapper;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbStorageDriver;
use PeekAndPoke\Component\Slumber\Data\StorageImpl;
use PeekAndPoke\Component\Slumber\Mocks\UnitTestServiceProvider;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestAggregatedClass;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestMainClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
abstract class SlumberMongoDbTestBase extends TestCase
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
    /** @var EntityRepository */
    static protected $journalRepo;

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
        self::$mainRepo->buildIndexes();

        self::$journalRepo = new EntityRepository(
            'ref_class',
            new MongoDbStorageDriver(
                $entityPool,
                $codecSet,
                $database->selectCollection(self::REFERENCED_COLLECTION),
                new \ReflectionClass(UnitTestAggregatedClass::class)
            )
        );
        self::$storage->addRepository(self::$journalRepo);
        self::$journalRepo->buildIndexes();

    }
}
