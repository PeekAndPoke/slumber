<?php
/**
 * File was created 07.10.2015 07:46
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon;

use PeekAndPoke\Component\Slumber\Annotation\Slumber;

/**
 * Add two properties createdAt and updatedAt that are filled on save and on update as expected.
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
trait SlumberTimestamped
{
    /**
     * @var \DateTime
     *
     * @Slumber\AsSimpleDate()
     * @Slumber\Store\AsCreatedAt()
     * @Slumber\Store\Indexed(background = true, direction = "DESC")
     * @Slumber\Store\Indexed(background = true, direction = "ASC")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @Slumber\AsSimpleDate()
     * @Slumber\Store\AsUpdatedAt()
     * @Slumber\Store\Indexed(background = true, direction = "DESC")
     * @Slumber\Store\Indexed(background = true, direction = "ASC")
     */
    protected $updatedAt;

    /**
     * @var float
     *
     * @Slumber\AsDecimal()
     * @Slumber\Store\AsUpdatedAtUsec()
     */
    protected $updatedAtUsec = 0;


    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt ? clone $this->createdAt : null;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt ? clone $this->updatedAt : null;
    }

    /**
     * @return float
     */
    public function getUpdatedAtUsec()
    {
        return $this->updatedAtUsec;
    }
}
