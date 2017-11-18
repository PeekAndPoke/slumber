<?php
/**
 * Created by gerk on 14.11.17 17:47
 */

namespace PeekAndPoke\Component\Slumber\Functional\Data\Journal;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\ArrayCache;
use MongoDB;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel\JournalEntry;
use PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel\RecordableHistory;
use PeekAndPoke\Component\Slumber\Data\Addon\Journal\JournalEntryRepositoryImpl;
use PeekAndPoke\Component\Slumber\Data\Addon\Journal\JournalWriter;
use PeekAndPoke\Component\Slumber\Data\Addon\Journal\JournalWriterImpl;
use PeekAndPoke\Component\Slumber\Data\EntityPoolImpl;
use PeekAndPoke\Component\Slumber\Data\EntityRepository;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbCodecSet;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbEntityConfigReaderCached;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbEntityConfigReaderImpl;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbPropertyMarkerToMapper;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbStorageDriver;
use PeekAndPoke\Component\Slumber\Data\StorageImpl;
use PeekAndPoke\Component\Slumber\Mocks\UnitTestServiceProvider;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestJournalizedClass;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class JournalWorksMongoDbFeatureTest extends TestCase
{
    const DB_NAME            = 'slumber_tests_db';
    const COLLECTION         = 'journalized_class';
    const JOURNAL_COLLECTION = 'journal_entries';

    /** @var StorageImpl */
    static protected $storage;
    /** @var MongoDB\Client */
    static protected $client;
    /** @var EntityRepository */
    static protected $mainRepo;
    /** @var EntityRepository */
    static protected $journalRepo;
    /** @var JournalWriter */
    static protected $journal;

    public static function setUpBeforeClass()
    {
        // setup the annotation reader for autoload
        AnnotationRegistry::registerLoader(
            function ($class) {
                return class_exists($class) || interface_exists($class) || trait_exists($class);
            }
        );

        $di               = new UnitTestServiceProvider();
        $logger           = new NullLogger();
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

        $codecSet = new MongoDbCodecSet($di, $entityConfigReader, self::$storage, $logger);

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Important we need to add the Journal writer to the DI before adding the other repo to the store.
        // The MongoDbRepository tries to read the entity config and fails if the journal writer is not known yet
        self::$journalRepo = new JournalEntryRepositoryImpl(
            self::JOURNAL_COLLECTION,
            new MongoDbStorageDriver(
                $entityPool,
                $codecSet,
                $database->selectCollection(self::JOURNAL_COLLECTION),
                new \ReflectionClass(JournalEntry::class)
            )
        );
        self::$storage->addRepository(self::$journalRepo);

        // set the journal writer on the DI
        self::$journal = new JournalWriterImpl(self::$storage, self::$journalRepo);
        $di->set(JournalWriter::SERVICE_ID, self::$journal);

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // Now we can add the repo that stores the journalized entity
        self::$mainRepo = new EntityRepository(
            self::COLLECTION,
            new MongoDbStorageDriver(
                $entityPool,
                $codecSet,
                $database->selectCollection(self::COLLECTION),
                new \ReflectionClass(UnitTestJournalizedClass::class)
            )
        );
        self::$storage->addRepository(self::$mainRepo);

        // setting up the indexes is like a little self-test
        self::$mainRepo->buildIndexes();
        self::$journalRepo->buildIndexes();
    }

    public function setUp()
    {
        self::$mainRepo->removeAll([]);
        self::$journalRepo->removeAll([]);
    }

    public function testSaveItemMustWriteJournalEntry()
    {
        // create and store an item twice ... record it twice in the journal
        $start = new \DateTime();
        $item  = $this->storeItemTwice();
        $end   = new \DateTime();

        $allJournalEntries = self::$journalRepo->find();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // where the journal entries written ?
        $this->assertCount(2, $allJournalEntries, 'Journal entry must be written');

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // is the history reloaded correctly ?
        $history = self::$journal->getHistory($item);

        $this->assertCount(2, $history->getRecords(), 'There must be two records when the journal is not compacted');

        $this->assertHistory($history, $start, $end);
    }

    public function testJournalCompactingWorks()
    {
        // create and store an item twice ... record it twice in the journal
        $start = new \DateTime();
        sleep(1); // we need to sleep one second since mongo does not store the micro-seconds
        $item = $this->storeItemTwice();
        sleep(1); // we need to sleep one second since mongo does not store the micro-seconds
        $end = new \DateTime();

        // execute the compacting
        self::$journal->compact(
            self::$journal->buildExternalReference($item)
        );

        // is the history correct ?
        $history = self::$journal->getHistory($item);

        $this->assertCount(1, $history->getRecords(), 'There must be one record when the journal is not compacted');

        $this->assertHistory($history, $start, $end);
    }

    public function testJournalStatsBeforeCompacting()
    {
        $this->storeItemTwice();

        $stats = self::$journal->getStats();

        $this->assertSame(0, $stats->getCompactedRate(), 'Stats must be correct before compacting');
        $this->assertSame(0, $stats->getNumCompacted(), 'Stats must be correct before compacting');
        $this->assertSame(2, $stats->getNumRecords(), 'Stats must be correct before compacting');
    }

    public function testJournalStatsAfterCompacting()
    {
        $this->storeItemTwice();

        self::$journal->compactOldest(1000);
        $stats = self::$journal->getStats();

        $this->assertSame(1, $stats->getCompactedRate(), 'Stats must be correct after compacting');
        $this->assertSame(1, $stats->getNumCompacted(), 'Stats must be correct after compacting');
        $this->assertSame(1, $stats->getNumRecords(), 'Stats must be correct after compacting');
    }

    /**
     * @return UnitTestJournalizedClass
     */
    private function storeItemTwice()
    {
        $item = new UnitTestJournalizedClass();
        $item->setName('name1');
        $item->setAge(1);
        self::$mainRepo->save($item);

        $item->setName('name2');
        $item->setAge(2);
        self::$mainRepo->save($item);

        return $item;
    }

    /**
     * @param RecordableHistory $history
     * @param \DateTime         $start
     * @param \DateTime         $end
     */
    private function assertHistory(RecordableHistory $history, \DateTime $start, \DateTime $end)
    {
        $initialRecord = $history->getInitialRecord();
        $this->assertNotNull($initialRecord, 'There must be an initial record in the journal history');

        $finalRecord = $history->getFinalRecord();
        $this->assertNotNull($finalRecord, 'There must be a final record in the journal history');

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // are the diffs correct ?
        $diffs = $history->getDiffs();
        $this->assertCount(2, $diffs, 'There must be 2 diffs in the journal history');

        // check that createdBy is set correctly
        // check that changedAt is set correctly
        $diff0 = $diffs[0];
        $this->assertSame('Admin UnitTestUser@127.0.0.1', $diff0->getChangedBy(), 'Diff must contain correct "createdBy"');
        $this->assertGreaterThanOrEqual($start, $diff0->getChangeDate(), 'changeDate must not be too early');
        $this->assertLessThanOrEqual($end, $diff0->getChangeDate(), 'changeDate must not be too late');

        $diff1 = $diffs[1];
        $this->assertSame('Admin UnitTestUser@127.0.0.1', $diff1->getChangedBy(), 'Diff must contain correct "createdBy"');
        $this->assertGreaterThanOrEqual($start, $diff1->getChangeDate(), 'changeDate must not be too early');
        $this->assertLessThanOrEqual($end, $diff1->getChangeDate(), 'changeDate must not be too late');

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // check the diffs for the 'age' property
        $diff0Age = $diff0->getChanges()['age'];
        $this->assertSame('', $diff0Age->getBefore(), 'The "before" value must correct');
        $this->assertSame('1', $diff0Age->getAfter(), 'The "after" value must correct');

        $diff1Age = $diffs[1]->getChanges()['age'];
        $this->assertSame('1', $diff1Age->getBefore(), 'The "before" value must correct');
        $this->assertSame('2', $diff1Age->getAfter(), 'The "after" value must correct');

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        // check the diffs for the 'name' property
        $diff0Age = $diffs[0]->getChanges()['name'];
        $this->assertSame('', $diff0Age->getBefore(), 'The "before" value must correct');
        $this->assertSame('name1', $diff0Age->getAfter(), 'The "after" value must correct');

        $diff1Age = $diffs[1]->getChanges()['name'];
        $this->assertSame('name1', $diff1Age->getBefore(), 'The "before" value must correct');
        $this->assertSame('name2', $diff1Age->getAfter(), 'The "after" value must correct');
    }
}
