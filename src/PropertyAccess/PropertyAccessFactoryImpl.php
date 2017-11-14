<?php
/**
 * Created by gerk on 13.11.17 08:06
 */

namespace PeekAndPoke\Component\PropertyAccess;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class PropertyAccessFactoryImpl implements PropertyAccessFactory
{
    /**
     * @param \ReflectionClass    $class
     * @param \ReflectionProperty $property
     *
     * @return PropertyAccess
     */
    public function create(\ReflectionClass $class, \ReflectionProperty $property)
    {
        if ($property->isStatic()) {
            throw new \LogicException('Cannot create property access for static properties');
        }

        // is it a public property
        if ($property->isPublic()) {
            return PublicPropertyAccess::create($property->name);
        }

        // can we you the reflection accessor
        if ($property->isProtected() ||
            $property->getDeclaringClass()->name === $class->name) {

            return ReflectionPropertyAccess::create($class, $property->name);
        }

        return ScopedPropertyAccess::create($property->getDeclaringClass()->name, $property->name);
    }
}
