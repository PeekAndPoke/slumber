<?php
/**
 * Created by gerk on 18.11.17 20:46
 */

namespace PeekAndPoke\Component\Slumber\Data;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class EntityPoolStats
{
    /** @var int */
    private $numEntries;
    /** @var int */
    private $numHits;
    /** @var int */
    private $numMisses;

    /**
     * @param int $numEntries
     * @param int $numHits
     * @param int $numMisses
     */
    public function __construct($numEntries, $numHits, $numMisses)
    {
        $this->numEntries = $numEntries;
        $this->numHits    = $numHits;
        $this->numMisses  = $numMisses;
    }

    /**
     * @return int
     */
    public function getNumEntries()
    {
        return $this->numEntries;
    }

    /**
     * @return int
     */
    public function getNumHits()
    {
        return $this->numHits;
    }

    /**
     * @return int
     */
    public function getNumMisses()
    {
        return $this->numMisses;
    }
}
