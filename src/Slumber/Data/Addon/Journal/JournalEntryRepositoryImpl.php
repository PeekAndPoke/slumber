<?php
/**
 * File was created 28.04.2016 08:30
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon\Journal;

use PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel\JournalEntry;
use PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel\Record;
use PeekAndPoke\Component\Slumber\Data\Cursor;
use PeekAndPoke\Component\Slumber\Data\EntityRepository;

/**
 * @method JournalEntry[]|Cursor find(array $query = null)
 * @method JournalEntry|null     findOne(array $query = null)
 * @method JournalEntry|null     findById($id)
 * @method JournalEntry|null     findByReference($reference)
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class JournalEntryRepositoryImpl extends EntityRepository implements JournalEntryRepository
{
    /**
     * @return Record
     */
    public function createRecord()
    {
        return new JournalEntry();
    }

    /**
     * @return int
     */
    public function getRecordsCount()
    {
        return $this->find()->count();
    }

    /**
     * @return int
     */
    public function getCompactedRecordsCount()
    {
        return $this->find([
            'isCompacted' => true,
        ])->count();
    }

    /**
     * @param string $externalReference
     *
     * @return JournalEntry[]|Cursor
     */
    public function findByExternalReference($externalReference)
    {
        $result = $this->find([
            'externalReference' => (string) $externalReference,
        ]);

        $result->sort(['_id' => 1]);

        return $result;
    }

    /**
     * @param int $limit Number of entries to get
     *
     * @return JournalEntry[]|Cursor
     */
    public function findOldestNotCompacted($limit)
    {
        $cursor = $this->find([
            'isCompacted' => ['$ne' => true],
        ]);

        $cursor->skip(0)->limit($limit);

        return $cursor;
    }
}
