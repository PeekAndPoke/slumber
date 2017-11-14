<?php
/**
 * File was created 05.02.2015 21:44
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon\Journal\DomainModel;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class RecordDiffEntry
{
    /** @var string */
    private $key;
    /** @var string */
    private $before;
    /** @var string */
    private $after;

    /**
     * @param string $key
     * @param string $before
     * @param string $after
     */
    public function __construct($key, $before, $after)
    {
        $this->key    = $key;
        $this->before = $before;
        $this->after  = $after;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getBefore()
    {
        return $this->before;
    }

    /**
     * @return string
     */
    public function getAfter()
    {
        return $this->after;
    }
}
