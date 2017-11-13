<?php
/**
 * Created by gerk on 13.11.17 06:06
 */

namespace PeekAndPoke\Component\PropertyAccess\Stubs;


/**
 * UnitTestDirectScopedPropertyAccessMainClass
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class UnitTestScopedPropertyAccessBaseClass
{
    /** @var string */
    private $prop1;
    /** @var string */
    private $prop2;

    /** @var mixed */
    public $publicPropOnBase;
    /** @var mixed */
    protected $protectedPropOnBase;

    /**
     * @return string
     */
    public function getProp1Shadowed()
    {
        return $this->prop1;
    }

    /**
     * @param string $prop1
     *
     * @return $this
     */
    public function setProp1Shadowed($prop1)
    {
        $this->prop1 = $prop1;

        return $this;
    }

    /**
     * @return string
     */
    public function getProp2Shadowed()
    {
        return $this->prop2;
    }

    /**
     * @param string $prop2
     *
     * @return $this
     */
    public function setProp2Shadowed($prop2)
    {
        $this->prop2 = $prop2;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublicPropOnBase()
    {
        return $this->publicPropOnBase;
    }

    /**
     * @param mixed $publicPropOnBase
     *
     * @return $this
     */
    public function setPublicPropOnBase($publicPropOnBase)
    {
        $this->publicPropOnBase = $publicPropOnBase;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProtectedPropOnBase()
    {
        return $this->protectedPropOnBase;
    }

    /**
     * @param mixed $protectedPropOnBase
     *
     * @return $this
     */
    public function setProtectedPropOnBase($protectedPropOnBase)
    {
        $this->protectedPropOnBase = $protectedPropOnBase;

        return $this;
    }
}
