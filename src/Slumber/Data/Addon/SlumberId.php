<?php
/**
 * File was created 30.09.2015 12:57
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon;

use PeekAndPoke\Component\Slumber\Annotation\Slumber;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
trait SlumberId
{
    /**
     * @var string
     *
     * @Slumber\AsString()
     * @Slumber\Store\AsId()
     */
    protected $id;

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = (string) $id;

        return $this;
    }
}
