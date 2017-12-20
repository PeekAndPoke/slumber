<?php
/**
 * File was created 17.05.2016 06:29
 */

namespace PeekAndPoke\Component\Creator;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
abstract class AbstractCreator implements Creator
{
    /** @var string */
    protected $fqcn;

    /**
     * WithoutConstructor constructor.
     *
     * @param \ReflectionClass $class
     */
    public function __construct(\ReflectionClass $class)
    {
        // ReflectionClass cannot be serialized (so we only store the FQCN)
        $this->fqcn = $class->name;
    }

    /**
     * @return string
     */
    public function getFqcn()
    {
        return $this->fqcn;
    }

    /**
     * @return \ReflectionClass
     */
    public function getClass()
    {
        static $registry = [];

        return $registry[$this->fqcn] ?? $registry[$this->fqcn] = new \ReflectionClass($this->fqcn);
    }
}
