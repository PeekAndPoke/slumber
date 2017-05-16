<?php
/**
 * File was created 14.05.2016 22:26
 */

namespace PeekAndPoke\Component\Slumber\Tests\Stubs;

use PeekAndPoke\Component\Slumber\Annotation\Slumber;

/**
 * @Slumber\Polymorphic(
 *     {
 *          "a": UnitTestPolyChildA::class,
 *          "b": UnitTestPolyChildB::class
 *     },
 *     tellBy = "type",
 *     default = UnitTestPolyChildC::class
 * )
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
abstract class UnitTestPolyParent
{
    /**
     * @var string
     *
     * @Slumber\AsString()
     */
    protected $common;

    /**
     * @return string
     */
    public function getCommon()
    {
        return $this->common;
    }

    /**
     * @param string $common
     *
     * @return $this
     */
    public function setCommon($common)
    {
        $this->common = $common;

        return $this;
    }
}
