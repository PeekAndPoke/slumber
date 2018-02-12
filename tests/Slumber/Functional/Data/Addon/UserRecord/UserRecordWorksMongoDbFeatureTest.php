<?php
/**
 * Created by gerk on 14.11.17 17:47
 */

namespace PeekAndPoke\Component\Slumber\Functional\Data\Addon\UserRecord;

use PeekAndPoke\Component\Slumber\Data\Addon\UserRecord\UserRecord;
use PeekAndPoke\Component\Slumber\Data\EntityRepository;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbStorageDriver;
use PeekAndPoke\Component\Slumber\Data\RepositoryRegistry\ProviderContext;
use PeekAndPoke\Component\Slumber\Data\RepositoryRegistryImpl;
use PeekAndPoke\Component\Slumber\Data\StorageImpl;
use PeekAndPoke\Component\Slumber\Functional\MongoDb\SlumberMongoDbTestBase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class UserRecordWorksMongoDbFeatureTest extends SlumberMongoDbTestBase
{
    public const DB_NAME    = 'slumber_tests_db';
    public const COLLECTION = 'user_record_collection';

    /** @var StorageImpl */
    static protected $storage;
    /** @var EntityRepository */
    static protected $mainRepo;

    public static function setUpBeforeClass()
    {
        $entityPool = static::createEntityPool();
        $registry   = new RepositoryRegistryImpl();

        self::$storage = new StorageImpl($entityPool, $registry);
        $codecSet      = static::createCodecSet(self::$storage);

        $registry->registerProvider(self::COLLECTION, [UnitTestUserRecordClass::class], function (ProviderContext $context) use ($entityPool, $codecSet) {

            $collection = static::createDatabase()->selectCollection($context->getName());
            $reflect    = $context->getFirstClass();

            return new EntityRepository($context->getName(), new MongoDbStorageDriver($entityPool, $codecSet, $collection, $reflect));
        });

        // set the journal writer on the DI
        // setting up the indexes is like a little self-test
        self::$mainRepo = self::$storage->getRepositoryByName(self::COLLECTION);
        self::$mainRepo->buildIndexes();
    }

    public function setUp()
    {
        self::$mainRepo->removeAll([]);
    }

    public function testSaveItemMustWriteJournalEntry()
    {
        $item = new UnitTestUserRecordClass();

        self::$mainRepo->save($item);

        $this->assertInstanceOf(UserRecord::class, $item->getCreatedBy());
    }
}
