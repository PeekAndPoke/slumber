<?php
/**
 * Created by gerk on 13.11.17 05:50
 */

namespace PeekAndPoke\Component\PropertyAccess;

/**
 * This implementation accesses properties through function call scoping.
 *
 * This is the slowest accessor with the fewest limitation. Consider using others when possible.
 *
 *
 * What can it do?
 *
 * - access public properties
 *  -> you should use PublicPropertyAccess for performance
 *
 * - access protected properties that are visible on the subject class
 *
 * - access private properties declared on the subject class
 *  -> use ReflectionPropertyAccess for performance
 *
 * - access private properties that are declared on a base class of the subject class
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ScopedPropertyAccess implements PropertyAccess
{
    /** @var string */
    private $scopeClass;
    /** @var string */
    private $propertyName;

    /** @var bool */
    private static $initialized = false;
    /** @var \Closure */
    private static $getAccess;
    /** @var \Closure */
    private static $setAccess;

    /**
     * @param string $scopeClass   Fqcn of the scope that declares the property
     * @param string $propertyName The name of the property
     *
     * @return ScopedPropertyAccess
     */
    public static function create($scopeClass, $propertyName)
    {
        $ret               = new self;
        $ret->scopeClass   = $scopeClass;
        $ret->propertyName = $propertyName;

        // we need to at least initialize this class ones
        if (self::$initialized === false) {
            $ret->init();
        }

        return $ret;
    }

    /**
     * Make use of the static create method
     */
    private function __construct() { }

    /**
     * Called on unserialze
     */
    public function __wakeup()
    {
        // we need to at least initialize this class ones
        if (self::$initialized === false) {
            $this->init();
        }
    }

    private function init()
    {
        self::$initialized = true;
        self::$getAccess   = \Closure::fromCallable(function ($prop) { return $this->$prop; });
        self::$setAccess   = \Closure::fromCallable(function ($prop, $value) { $this->$prop = $value; });
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
        $func = self::$getAccess->bindTo($subject, $this->scopeClass);

        return $func($this->propertyName);
    }

    /**
     * Set the value of the property
     *
     * @param mixed $subject The object to set the value to
     * @param mixed $value
     */
    public function set($subject, $value)
    {
        $func = self::$setAccess->bindTo($subject, $this->scopeClass);
        $func($this->propertyName, $value);
    }
}
