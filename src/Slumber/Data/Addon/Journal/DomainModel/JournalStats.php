<?php
/**
 * File was created 23.04.2015 16:31
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class JournalStats
{
    /** @var int */
    private $numRecords;
    /** @var int */
    private $numCompacted;

    /**
     * @param int $numRecords
     * @param int $numCompacted
     */
    public function __construct($numRecords, $numCompacted)
    {
        $this->numRecords   = $numRecords;
        $this->numCompacted = $numCompacted;
    }

    /**
     * @return int
     */
    public function getNumRecords()
    {
        return $this->numRecords;
    }

    /**
     * @return int
     */
    public function getNumCompacted()
    {
        return $this->numCompacted;
    }

    /**
     * @return float
     */
    public function getCompactedRate()
    {
        if ($this->numRecords === 0) {
            return 1.0;
        }

        return $this->numCompacted / $this->numRecords;
    }
}
