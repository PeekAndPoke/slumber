<?php
/**
 * Created by gerk on 09.11.17 16:28
 */

namespace PeekAndPoke\Component\Slumber\Stubs;

use PeekAndPoke\Component\Slumber\Annotation\Slumber;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class UnitTestClassInheritingAPrivateProperty extends UnitTestBaseClassWithPrivateProperty
{
    /**
     * @var string
     *
     * @Slumber\AsString()
     */
    private $other;

    /**
     * @return mixed
     */
    public function getOther()
    {
        return $this->other;
    }

    /**
     * @param mixed $other
     *
     * @return $this
     */
    public function setOther($other)
    {
        $this->other = $other;

        return $this;
    }
}
