<?php
/**
 * File was created 05.02.2015 21:29
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class RecordDiff
{
    /** @var \DateTime */
    private $changeDate;
    /** @var string */
    private $changedBy;
    /** @var RecordDiffEntry[] */
    private $changes = [];

    /**
     * @param \DateTime $changeDate
     * @param string    $changedBy
     */
    public function __construct(\DateTime $changeDate, $changedBy)
    {
        $this->changeDate = $changeDate;
        $this->changedBy  = $changedBy;
    }

    /**
     * @return \DateTime
     */
    public function getChangeDate()
    {
        return $this->changeDate;
    }

    /**
     * @return string
     */
    public function getChangedBy()
    {
        return $this->changedBy;
    }

    /**
     * @return RecordDiffEntry[]
     */
    public function getChanges()
    {
        ksort($this->changes);

        return $this->changes;
    }

    /**
     * @return int
     */
    public function getChangesCount()
    {
        return \count($this->changes);
    }

    /**
     * @param RecordDiffEntry $change
     *
     * @return $this
     */
    public function addChange(RecordDiffEntry $change)
    {
        $this->changes[$change->getKey()] = $change;

        return $this;
    }
}
