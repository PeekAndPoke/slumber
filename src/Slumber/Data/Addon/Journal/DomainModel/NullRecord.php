<?php
/**
 * File was created 05.02.2015 23:57
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class NullRecord implements Record
{
    /**
     * {@inheritdoc}
     */
    public function getExternalReference()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setExternalReference($ref)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChangeDate()
    {
        return new \DateTime('now');
    }

    /**
     * {@inheritdoc}
     */
    public function setChangeDate(\DateTime $date)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getChangedBy()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getRecordData()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getIsCompacted()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompactedHistory()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setCompactedHistory(RecordableHistory $history)
    {
        return $this;
    }
}
