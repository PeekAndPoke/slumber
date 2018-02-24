<?php
/**
 * Created by gerk on 24.02.18 10:12
 */

namespace PeekAndPoke\Component\Slumber\Stubs;

use PeekAndPoke\Component\Slumber\Annotation\Slumber;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class UnitTestSlumberMainClassBase
{
    /**
     * @var string|null
     *
     * @Slumber\AsString()
     */
    private $privateOnBase;

    /**
     * @return null|string
     */
    public function getPrivateOnBase()
    {
        return $this->privateOnBase;
    }

    /**
     * @param null|string $privateOnBase
     *
     * @return $this
     */
    public function setPrivateOnBase($privateOnBase)
    {
        $this->privateOnBase = $privateOnBase;

        return $this;
    }
}
