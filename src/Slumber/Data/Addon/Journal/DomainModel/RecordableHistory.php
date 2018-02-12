<?php

namespace PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class RecordableHistory
{
    /** @var Record[] */
    private $records;
    /** @var RecordDiff[] */
    private $diffs;

    /**
     * @param Record       $initial
     * @param Record       $final
     * @param RecordDiff[] $diffs
     *
     * @return RecordableHistory
     */
    public static function fromInitialAndFinalAndDiffs(Record $initial, Record $final, $diffs)
    {
        $ret        = new RecordableHistory([$initial, $final]);
        $ret->diffs = $diffs;

        return $ret;
    }

    /**
     * @param Record[] $records
     */
    public function __construct($records)
    {
        $this->records = $records;
    }

    /**
     * @return float
     */
    public function getCompactionRate()
    {
        $compacted    = 0;
        $notCompacted = 0;

        foreach ($this->records as $record) {
            if ($record->getIsCompacted() === false || $record->getCompactedHistory() === null) {
                $notCompacted++;
            } else {
                $compacted += \count($record->getCompactedHistory()->getDiffs());
            }
        }

        if ($compacted + $notCompacted === 0) {
            return 0;
        }

        return $compacted / ($compacted + $notCompacted);
    }

    /**
     * @return Record
     */
    public function getInitialRecord()
    {
        $record = \count($this->records) > 0 ? $this->records[0] : new NullRecord();

        if ($record->getIsCompacted() &&
            $record->getCompactedHistory() !== null
        ) {
            return $record->getCompactedHistory()->getInitialRecord();
        }

        return $record;
    }

    /**
     * @return Record
     */
    public function getFinalRecord()
    {
        $record = \count($this->records) > 0 ? $this->records[\count($this->records) - 1] : new NullRecord();

        if ($record->getIsCompacted() &&
            $record->getCompactedHistory() !== null) {
            return $record->getCompactedHistory()->getFinalRecord();
        }

        return $record;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecords()
    {
        return $this->records;
    }

    /**
     * @return RecordDiff[]
     */
    public function getDiffs()
    {
        if ($this->diffs === null) {
            $this->calculateDiffs();
        }

        return $this->diffs;
    }

    protected function calculateDiffs()
    {
        $this->diffs = [];

        $previous = new NullRecord();

        foreach ($this->records as $current) {

            if ($current->getIsCompacted() === false) {
                $diff = $this->calculateDiff($previous, $current);

                if ($diff->getChangesCount() > 0) {

                    $this->diffs[] = $diff;
                }

                $previous = $current;

            } else {

                $history = $current->getCompactedHistory();

                if ($history) {
                    foreach ($history->getDiffs() as $diff) {
                        $this->diffs[] = $diff;
                    }
                }

                $previous = $history->getFinalRecord();
            }
        }
    }

    /**
     * @param Record $before
     * @param Record $after
     *
     * @return RecordDiff
     */
    protected function calculateDiff(Record $before, Record $after)
    {
        $diff = new RecordDiff($after->getChangeDate(), $after->getChangedBy());

        $beforeData = (array) $before->getRecordData();
        $afterData  = (array) $after->getRecordData();

        $this->diffRecursiveFromBeforeToAfter($diff, '', $beforeData, $afterData);
        $this->diffRecursiveFromAfterToBefore($diff, '', $beforeData, $afterData);

        return $diff;
    }

    /**
     * @param RecordDiff $diff
     * @param string     $pathInArray
     * @param mixed[]    $before
     * @param mixed[]    $after
     */
    private function diffRecursiveFromBeforeToAfter(RecordDiff $diff, $pathInArray, $before, $after)
    {
        if (! \is_array($before) && ! $before instanceof \Traversable) {

            $beforeValue = $this->convertToString($before);
            $afterValue  = $this->convertToString($after);

            if ($beforeValue !== $afterValue) {
                $diff->addChange(new RecordDiffEntry($pathInArray, $beforeValue, $afterValue));
            }
        } else {

            foreach ($before as $key => $beforeValue) {

                $afterValue = null;

                if ((\is_array($after) || $after instanceof \ArrayAccess) && isset($after[$key])) {
                    $afterValue = $after[$key];
                }

                $this->diffRecursiveFromBeforeToAfter(
                    $diff,
                    empty($pathInArray) ? $key : $pathInArray . '.' . $key,
                    $beforeValue,
                    $afterValue
                );
            }
        }
    }

    /**
     * @param RecordDiff $diff
     * @param string     $pathInArray
     * @param array      $before
     * @param array      $after
     */
    private function diffRecursiveFromAfterToBefore(RecordDiff $diff, $pathInArray, $before, $after)
    {
        if (! \is_array($after) && ! $after instanceof \Traversable) {

            $beforeValue = $this->convertToString($before);
            $afterValue  = $this->convertToString($after);

            if ($beforeValue !== $afterValue) {
                $diff->addChange(new RecordDiffEntry($pathInArray, $beforeValue, $afterValue));
            }
        } else {

            // first compare from before to after
            foreach ($after as $key => $afterValue) {

                $beforeValue = null;

                if ((\is_array($before) || $before instanceof \ArrayAccess) && isset($before[$key])) {
                    $beforeValue = $before[$key];
                }

                $this->diffRecursiveFromAfterToBefore(
                    $diff,
                    empty($pathInArray) ? $key : $pathInArray . '.' . $key,
                    $beforeValue,
                    $afterValue
                );
            }
        }
    }

    /**
     * @param        $value
     * @param string $fallback
     *
     * @return string
     */
    private function convertToString($value, $fallback = 'N/A')
    {
        try {
            if ($value === null) {
                return '';
            }

            if (is_scalar($value)) {
                return (string) $value;
            }

            if (\is_array($value)) {
                return json_encode($value, JSON_PRETTY_PRINT);
            }

            if ($value instanceof \DateTime) {
                return $value->format('c');
            }

            return (string) $fallback;

        } catch (\Exception $e) {
            return $fallback;
        }
    }
}
