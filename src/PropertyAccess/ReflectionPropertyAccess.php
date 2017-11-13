<?php
/**
 * Created by gerk on 13.11.17 05:50
 */

namespace PeekAndPoke\Component\PropertyAccess;

/**
 * This implementation accesses properties through reflection.
 *
 * This is a medium speed accessor which less limitation. It will also be the most used one since most
 * class properties are protected or private.
 *
 *
 * What can it do?
 *
 * - access public properties (you should use PublicPropertyAccess instead for performance)
 *
 * - access protected properties that are visible on the subject class
 *
 * - access private properties declared on the subject class
 *
 * What can it NOT do?
 *
 * - access private properties that are declared on a base class of the subject class
 *  -> use ScopedPropertyAccess then which is the slowest accessor
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ReflectionPropertyAccess implements PropertyAccess
{
    /** @var string */
    private $className;
    /** @var string */
    private $propertyName;

    /** @var \ReflectionProperty */
    private $_prop;

    /**
     * @param \ReflectionClass $class
     * @param string           $propertyName The name of the property
     *
     * @return ReflectionPropertyAccess
     */
    public static function create(\ReflectionClass $class, $propertyName)
    {
        $ret               = new self;
        $ret->className    = $class->getName();
        $ret->propertyName = $propertyName;

        $ret->_prop = $class->getProperty($propertyName);
        $ret->_prop->setAccessible(true);

        return $ret;
    }

    /**
     * Make use of the static create method
     */
    private function __construct() { }

    /**
     * @return array
     */
    public function __sleep()
    {
        return ['className', 'propertyName'];
    }

    /**
     *
     */
    public function __wakeup()
    {
        $class = new \ReflectionClass($this->className);

        $this->_prop = $class->getProperty($this->propertyName);
        $this->_prop->setAccessible(true);
    }

    /**
     * Get the value of the property
     *
     * @param mixed $subject The object to get the value from
     *
     * @return mixed
     */
    public function get($subject)
    {
        return $this->_prop->getValue($subject);
    }

    /**
     * Set the value of the property
     *
     * @param mixed $subject The object to set the value to
     * @param mixed $value
     */
    public function set($subject, $value)
    {
        $this->_prop->setValue($subject, $value);
    }
}
