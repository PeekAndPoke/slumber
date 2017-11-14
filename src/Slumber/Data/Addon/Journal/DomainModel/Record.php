<?php
/**
 * File was created 05.02.2015 20:34
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface Record
{
    /**
     * @return string
     */
    public function getExternalReference();

    /**
     * @param string $ref
     *
     * @return $this
     */
    public function setExternalReference($ref);

    /**
     * Get the date of record creation
     *
     * @return \DateTime
     */
    public function getChangeDate();

    /**
     * @param \DateTime $date
     *
     * @return $this
     */
    public function setChangeDate(\DateTime $date);

    /**
     * @return string
     */
    public function getChangedBy();

    /**
     * Get the data
     *
     * @return array
     */
    public function getRecordData();

    /**
     * @return bool
     */
    public function getIsCompacted();

    /**
     * @return RecordableHistory|null
     */
    public function getCompactedHistory();

    /**
     * @param RecordableHistory $history
     *
     * @return $this
     */
    public function setCompactedHistory(RecordableHistory $history);
}
