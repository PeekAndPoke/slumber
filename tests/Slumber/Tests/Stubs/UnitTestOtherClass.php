<?php
/**
 * File was created 12.10.2015 06:39
 */

namespace PeekAndPoke\Component\Slumber\Tests\Stubs;

use PeekAndPoke\Component\Slumber\Annotation\Slumber;
use PeekAndPoke\Component\Slumber\Data\Addon\PublicReference\SlumberReferenced;
use PeekAndPoke\Component\Slumber\Data\Addon\SlumberTimestamped;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class UnitTestOtherClass
{
    use SlumberReferenced;
    use SlumberTimestamped;

    /**
     * @var string
     *
     * @Slumber\AsString()
     */
    private $otherName;

    /**
     * @return string
     */
    public function getOtherName()
    {
        return $this->otherName;
    }

    /**
     * @param string $otherName
     *
     * @return $this
     */
    public function setOtherName($otherName)
    {
        $this->otherName = $otherName;

        return $this;
    }
}
