<?php
/**
 * File was created 28.04.2016 08:28
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel;

use PeekAndPoke\Component\Slumber\Annotation\Slumber;
use PeekAndPoke\Component\Slumber\Data\Addon\SlumberId;
use PeekAndPoke\Component\Slumber\Data\Addon\SlumberTimestamped;
use PeekAndPoke\Component\Slumber\Data\Addon\UserRecord\SlumberRecordUser;
use PeekAndPoke\Component\Slumber\Data\Addon\UserRecord\UserRecord;
use PeekAndPoke\Component\Toolbox\ArrayUtil;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class JournalEntry implements Record
{
    use SlumberId;
    use SlumberTimestamped;
    use SlumberRecordUser;

    /**
     * @param string $externalReference
     * @param array  $recordData
     *
     * @return JournalEntry
     */
    public static function create($externalReference, $recordData)
    {
        $ret                    = new static;
        $ret->externalReference = $externalReference;
        $ret->recordData        = $recordData;

        return $ret;
    }

    /**
     * @var array
     *
     * @Slumber\AsIs()
     */
    private $recordData = [];

    /**
     * @var string
     *
     * @Slumber\AsString()
     * @Slumber\Store\Indexed(background = true)
     */
    private $externalReference;

    /**
     * @var bool
     *
     * @Slumber\AsBool()
     * @Slumber\Store\Indexed(background = true)
     */
    private $isCompacted = false;

    /**
     * @var array
     *
     * @Slumber\AsIs()
     */
    private $compactedData = [];

    /**
     * JournalEntry constructor.
     */
    public function __construct()
    {
        $this->createdBy = new UserRecord();
    }

    /**
     * @return string
     */
    public function getChangedBy()
    {
        return $this->getCreatedBy()->getRole()
               . ' ' . $this->getCreatedBy()->getName()
               . '@' . $this->getCreatedBy()->getIp();
    }

    /**
     * Get the date of record creation
     *
     * @return \DateTime
     */
    public function getChangeDate()
    {
        return $this->getCreatedAt();
    }

    /**
     * @param \DateTime $date
     *
     * @return $this
     */
    public function setChangeDate(\DateTime $date)
    {
        $this->createdAt = $date;

        return $this;
    }

    /**
     * @return array
     */
    public function getRecordData()
    {
        return $this->recordData;
    }

    /**
     * @param array $recordData
     *
     * @return $this
     */
    public function setRecordData($recordData)
    {
        $this->recordData = $recordData;

        return $this;
    }

    /**
     * @return string
     */
    public function getExternalReference()
    {
        return $this->externalReference;
    }

    /**
     * @param string $externalReference
     *
     * @return $this
     */
    public function setExternalReference($externalReference)
    {
        $this->externalReference = $externalReference;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getIsCompacted()
    {
        return $this->isCompacted;
    }

    /**
     * @param boolean $isCompacted
     *
     * @return $this
     */
    public function setIsCompacted($isCompacted)
    {
        $this->isCompacted = $isCompacted;

        return $this;
    }

    /**
     * @return array
     */
    public function getCompactedData()
    {
        return $this->compactedData;
    }

    /**
     * @param array $compactedData
     *
     * @return $this
     */
    public function setCompactedData($compactedData)
    {
        $this->compactedData = $compactedData;

        return $this;
    }

    /**
     * @return RecordableHistory
     */
    public function getCompactedHistory()
    {
        $data = $this->getCompactedData();

        $initialRecord = new JournalEntry();
        $initialRecord->setChangeDate(
            new \DateTime(ArrayUtil::getNested($data, 'initialRecord.changeDate', 'now'))
        );
        $initialRecord->setRecordData(ArrayUtil::getNested($data, 'initialRecord.recordData', []));

        $finalRecord = new JournalEntry();
        $finalRecord->setChangeDate(
            new \DateTime(ArrayUtil::getNested($data, 'finalRecord.changeDate', 'now'))
        );
        $finalRecord->setRecordData(ArrayUtil::getNested($data, 'finalRecord.recordData', []));

        $diffs = [];

        /** @noinspection ForeachSourceInspection */
        foreach (ArrayUtil::getNested($data, 'diffs', []) as $diffData) {

            $diff = new RecordDiff(
                new \DateTime(ArrayUtil::getNested($diffData, 'changeDate')),
                ArrayUtil::getNested($diffData, 'changedBy')
            );

            /** @noinspection ForeachSourceInspection */
            foreach (ArrayUtil::getNested($diffData, 'changes', []) as $changeData) {

                $key    = ArrayUtil::getNested($changeData, 'key');
                $before = ArrayUtil::getNested($changeData, 'before');
                $after  = ArrayUtil::getNested($changeData, 'after');

                $diff->addChange(new RecordDiffEntry($key, $before, $after));
            }

            $diffs[] = $diff;
        }

        $history = RecordableHistory::fromInitialAndFinalAndDiffs($initialRecord, $finalRecord, $diffs);

        return $history;
    }

    /**
     * @param RecordableHistory $history
     *
     * @return $this
     */
    public function setCompactedHistory(RecordableHistory $history)
    {
        $this->setIsCompacted(true);

        $initial = $history->getInitialRecord();
        $final   = $history->getFinalRecord();

        $data = [
            'initialRecord' => [
                'changeDate' => $initial->getChangeDate()->format('c'),
                'changedBy'  => $initial->getChangedBy(),
                'recordData' => $initial->getRecordData(),
            ],
            'finalRecord'   => [
                'changeDate' => $final->getChangeDate()->format('c'),
                'changedBy'  => $final->getChangedBy(),
                'recordData' => $final->getRecordData(),
            ],
            'diffs'         => [],
        ];

        foreach ($history->getDiffs() as $diff) {
            $diffData = [
                'changeDate' => $diff->getChangeDate()->format('c'),
                'changedBy'  => $diff->getChangedBy(),
                'changes'    => [],
            ];

            foreach ($diff->getChanges() as $change) {

                $diffData['changes'][] = [
                    'key'    => $change->getKey(),
                    'before' => $change->getBefore(),
                    'after'  => $change->getAfter(),
                ];
            }

            $data['diffs'][] = $diffData;
        }

        $this->setCompactedData($data);

        return $this;
    }

}
