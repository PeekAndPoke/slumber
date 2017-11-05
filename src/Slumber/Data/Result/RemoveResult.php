<?php
/**
 * Created by gerk on 05.11.17 17:44
 */

namespace PeekAndPoke\Component\Slumber\Data\Result;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class RemoveResult
{
    /** @var int */
    private $deletedCount;
    /** @var bool */
    private $acknowledged;

    /**
     * @param int  $deletedCount
     * @param bool $acknowledged
     */
    public function __construct($deletedCount, $acknowledged)
    {
        $this->deletedCount = $deletedCount;
        $this->acknowledged = $acknowledged;
    }

    /**
     * @return int
     */
    public function getDeletedCount()
    {
        return $this->deletedCount;
    }

    /**
     * @return bool
     */
    public function isAcknowledged()
    {
        return $this->acknowledged;
    }
}
