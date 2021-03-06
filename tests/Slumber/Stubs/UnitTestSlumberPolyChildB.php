<?php
/**
 * File was created 14.05.2016 22:28
 */

namespace PeekAndPoke\Component\Slumber\Stubs;

use PeekAndPoke\Component\Slumber\Annotation\Slumber;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class UnitTestSlumberPolyChildB extends UnitTestSlumberPolyParent
{
    /**
     * @var string
     *
     * @Slumber\AsString()
     */
    private $type = 'b';

    /**
     * @var string
     *
     * @Slumber\AsString()
     */
    private $propOnB;

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getPropOnB()
    {
        return $this->propOnB;
    }

    /**
     * @param string $propOnB
     *
     * @return $this
     */
    public function setPropOnB($propOnB)
    {
        $this->propOnB = $propOnB;

        return $this;
    }
}
