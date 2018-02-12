<?php
/**
 * File was created 28.04.2016 08:16
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon\Journal;

use PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel\JournalStats;
use PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel\RecordableHistory;
use PeekAndPoke\Component\Slumber\Data\Addon\Journal\Exception\JournalRuntimeException;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface JournalWriter
{
    public const SERVICE_ID = 'slumber.data.addon.journal_writer';

    /**
     * @param mixed $subject
     * @param array $serializedData
     */
    public function write($subject, $serializedData);

    /**
     * @return JournalStats
     */
    public function getStats();

    /**
     * @param mixed|string $subject
     *
     * @return RecordableHistory
     */
    public function getHistory($subject);

    /**
     * @param string $externalReference
     *
     * @return RecordableHistory
     *
     * @throws \Exception
     */
    public function compact($externalReference);

    /**
     * Compacts the given number of recorded journal histories
     *
     * @param int $batchSize
     */
    public function compactOldest($batchSize);

    /**
     * @param mixed $subject
     *
     * @return string
     * @throws JournalRuntimeException
     */
    public function buildExternalReference($subject);
}
