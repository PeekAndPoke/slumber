<?php
/**
 * File was created 28.04.2016 07:05
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon\Journal;

use PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel\JournalEntry;
use PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel\JournalStats;
use PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel\Record;
use PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel\RecordableHistory;
use PeekAndPoke\Component\Slumber\Data\Addon\Journal\Exception\JournalRuntimeException;
use PeekAndPoke\Component\Slumber\Data\Storage;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class JournalWriterImpl implements JournalWriter
{
    /** @var Storage */
    private $storage;
    /** @var JournalEntryRepository */
    private $repository;
    /** @var LoggerInterface */
    private $logger;

    /**
     * JournalWriter constructor.
     *
     * @param Storage                $storage
     * @param JournalEntryRepository $repository
     * @param LoggerInterface        $logger
     */
    public function __construct(Storage $storage, JournalEntryRepository $repository, LoggerInterface $logger = null)
    {
        $this->storage    = $storage;
        $this->repository = $repository;
        $this->logger     = $logger ?: new NullLogger();
    }

    /**
     * @param mixed $subject
     * @param array $serializedData
     */
    public function write($subject, $serializedData)
    {
        $entry = JournalEntry::create(
            $this->buildExternalReference($subject),
            $serializedData
        );

        $this->repository->save($entry);
    }

    /**
     * @return JournalStats
     */
    public function getStats()
    {
        $numRecords   = $this->repository->getRecordsCount();
        $numCompacted = $this->repository->getCompactedRecordsCount();

        return new JournalStats($numRecords, $numCompacted);
    }

    /**
     * @param mixed|string $subject
     *
     * @return RecordableHistory
     */
    public function getHistory($subject)
    {
        $externalRef = $this->buildExternalReference($subject);

        /** @var Record[] $records */
        $records = $this->repository->findByExternalReference($externalRef)->toArray();

        return new RecordableHistory($records);
    }

    /**
     * @param string $externalReference
     *
     * @return RecordableHistory
     *
     * @throws \Exception
     */
    public function compact($externalReference)
    {
        $history = $this->getHistory($externalReference);

        // create the compacted entry
        $compactedEntry = $this->repository->createRecord();
        $compactedEntry->setChangeDate($history->getFinalRecord()->getChangeDate());
        $compactedEntry->setExternalReference($externalReference);
        $compactedEntry->setCompactedHistory($history);

        $this->repository->save($compactedEntry);

        // delete all the other entries
        foreach ($history->getRecords() as $record) {
            $this->repository->remove($record);
        }

        return $history;
    }

    /**
     * Compacts the given number of recorded journal histories
     *
     * @param int $batchSize
     */
    public function compactOldest($batchSize)
    {
        $subBatchSize = 1000;

        for ($i = 0; $i < $batchSize; $i += $subBatchSize) {

            $oldestEntries = $this->repository->findOldestNotCompacted($subBatchSize);

            $this->logger->info('Will compact ' . ($i + $subBatchSize) . ' / ' . $batchSize . ' / ' . \count($oldestEntries) . ' of the oldest entries');

            if (\count($oldestEntries) === 0) {
                return;
            }

            foreach ($oldestEntries as $oldest) {

                $extRef = $oldest->getExternalReference();

                try {
                    $history = $this->compact($extRef);

                    $this->logger->info('Compacted ' . $extRef . ' with ' . \count($history->getDiffs()) . ' entries');
                } catch (\Exception $e) {
                    $this->logger->error('Cannot compact ' . $extRef . ': ' . $e->getMessage());
                }
            }

            // do not hold to many references and free up memory
            $this->storage->getEntityPool()->clear();
        }
    }

    /**
     * @param mixed|string $subject
     *
     * @return string
     * @throws JournalRuntimeException
     */
    public function buildExternalReference($subject)
    {
        if (is_scalar($subject)) {
            return (string) $subject;
        }

        $repo = $this->storage->getRepositoryByEntity($subject);

        if ($repo === null) {
            return null;
        }

        $repoName = $repo->getName();
        $reflect  = new \ReflectionClass($subject);

        if ($reflect->hasProperty('reference')) {
            $prop = $reflect->getProperty('reference');
        } else if ($reflect->hasProperty('id')) {
            $prop = $reflect->getProperty('id');
        } else {
            throw new JournalRuntimeException('Cannot calculate external reference for ' . $reflect->name . '. Needs property "id" or "reference"');
        }

        $prop->setAccessible(true);

        return $repoName . '-' . (string) $prop->getValue($subject);
    }
}
