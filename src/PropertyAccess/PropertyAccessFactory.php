<?php
/**
 * Created by gerk on 13.11.17 08:05
 */

namespace PeekAndPoke\Component\PropertyAccess;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface PropertyAccessFactory
{
    /**
     * @param \ReflectionClass    $class
     * @param \ReflectionProperty $property
     *
     * @return PropertyAccess
     */
    public function create(\ReflectionClass $class, \ReflectionProperty $property);
}
