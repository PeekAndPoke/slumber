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
class UnitTestScopedPropertyAccessMainClass extends UnitTestScopedPropertyAccessBaseClass
{
    /** @noinspection ClassOverridesFieldOfSuperClassInspection */
    /** @var string */
    private $prop1;
    /** @noinspection ClassOverridesFieldOfSuperClassInspection */
    /** @var string */
    private $prop2;

    /** @var mixed */
    public $publicProp;
    /** @var mixed */
    protected $protectedProp;

    /**
     * @return string
     */
    public function getProp1()
    {
        return $this->prop1;
    }

    /**
     * @param string $prop1
     *
     * @return $this
     */
    public function setProp1($prop1)
    {
        $this->prop1 = $prop1;

        return $this;
    }

    /**
     * @return string
     */
    public function getProp2()
    {
        return $this->prop2;
    }

    /**
     * @param string $prop2
     *
     * @return $this
     */
    public function setProp2($prop2)
    {
        $this->prop2 = $prop2;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPublicProp()
    {
        return $this->publicProp;
    }

    /**
     * @param mixed $publicProp
     *
     * @return $this
     */
    public function setPublicProp($publicProp)
    {
        $this->publicProp = $publicProp;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getProtectedProp()
    {
        return $this->protectedProp;
    }

    /**
     * @param mixed $protectedProp
     *
     * @return $this
     */
    public function setProtectedProp($protectedProp)
    {
        $this->protectedProp = $protectedProp;

        return $this;
    }
}
