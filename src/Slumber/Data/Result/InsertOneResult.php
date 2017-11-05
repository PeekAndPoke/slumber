<?php
/**
 * Created by gerk on 05.11.17 16:26
 */

namespace PeekAndPoke\Component\Slumber\Data\Result;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class InsertOneResult
{
    /** @var string */
    private $insertId;
    /** @var bool */
    private $acknowledged;

    /**
     * @param string $insertId
     * @param bool   $acknowledged
     */
    public function __construct($insertId, $acknowledged)
    {
        $this->insertId     = $insertId;
        $this->acknowledged = $acknowledged;
    }

    /**
     * @return string
     */
    public function getInsertId()
    {
        return $this->insertId;
    }

    /**
     * @return bool
     */
    public function isAcknowledged()
    {
        return $this->acknowledged;
    }
}
