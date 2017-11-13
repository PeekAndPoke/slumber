<?php
/**
 * Created by gerk on 13.11.17 05:50
 */

namespace PeekAndPoke\Component\PropertyAccess;

/**
 * This implementation accesses public properties directly.
 *
 * This is the fastest but also the most limited accessor.
 *
 *
 * What can it do?
 *
 * - access public properties
 *
 * What can it NOT do?
 *
 * - access protected properties that are visible on the subject class
 *  -> use ReflectionPropertyAccess
 *
 * - access private properties declared on the subject class
 *  -> use ReflectionPropertyAccess
 *
 * - access private properties that are declared on a base class of the subject class
 *  -> use ScopedPropertyAccess then which is the slowest accessor
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class PublicPropertyAccess implements PropertyAccess
{
    /** @var string */
    private $propertyName;

    /**
     * @param string $propertyName The name of the property
     *
     * @return PublicPropertyAccess
     */
    public static function create($propertyName)
    {
        $ret               = new self;
        $ret->propertyName = $propertyName;

        return $ret;
    }

    /**
     * Make use of the static create method
     */
    private function __construct() { }

    /**
     * Get the value of the property
     *
     * @param mixed $subject The object to get the value from
     *
     * @return mixed
     */
    public function get($subject)
    {
        return $subject->{$this->propertyName};
    }

    /**
     * Set the value of the property
     *
     * @param mixed $subject The object to set the value to
     * @param mixed $value
     */
    public function set($subject, $value)
    {
        $subject->{$this->propertyName} = $value;
    }
}
