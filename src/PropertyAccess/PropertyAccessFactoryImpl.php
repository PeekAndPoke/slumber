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
            return PublicPropertyAccess::create($property->getName());
        }


        // can we you the reflection accessor
        if ($property->isProtected()
            || $property->getDeclaringClass()->getName() === $class->getName()) {

            return ReflectionPropertyAccess::create($class, $property->getName());
        }

        return ScopedPropertyAccess::create($property->getDeclaringClass()->getName(), $property->getName());
    }
}
