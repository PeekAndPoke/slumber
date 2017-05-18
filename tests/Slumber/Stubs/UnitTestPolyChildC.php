<?php
/**
 * File was created 14.05.2016 22:28
 */

namespace PeekAndPoke\Component\Slumber\Stubs;

use PeekAndPoke\Component\Slumber\Annotation\Slumber;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class UnitTestPolyChildC extends UnitTestPolyParent
{
    /**
     * @var string
     *
     * @Slumber\AsString()
     */
    private $type = '';

    /**
     * @var string
     *
     * @Slumber\AsString()
     */
    private $propOnC;

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
    public function getPropOnC()
    {
        return $this->propOnC;
    }

    /**
     * @param string $propOnC
     *
     * @return $this
     */
    public function setPropOnC($propOnC)
    {
        $this->propOnC = $propOnC;

        return $this;
    }
}
