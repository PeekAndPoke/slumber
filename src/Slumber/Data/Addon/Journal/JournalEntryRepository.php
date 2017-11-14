<?php
/**
 * File was created 28.04.2016 08:41
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon\Journal;

use PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel\JournalEntry;
use PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel\Record;
use PeekAndPoke\Component\Slumber\Data\Cursor;
use PeekAndPoke\Component\Slumber\Data\Repository;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface JournalEntryRepository extends Repository
{
    /**
     * @return Record
     */
    public function createRecord();

    /**
     * @return int
     */
    public function getRecordsCount();

    /**
     * @return int
     */
    public function getCompactedRecordsCount();

    /**
     * @param string $externalReference
     *
     * @return JournalEntry[]|Cursor
     */
    public function findByExternalReference($externalReference);

    /**
     * @param int $limit Number of entries to get
     *
     * @return JournalEntry[]|Cursor
     */
    public function findOldestNotCompacted($limit);
}
