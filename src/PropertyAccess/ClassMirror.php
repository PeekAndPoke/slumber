<?php
/**
 * Created by gerk on 24.11.16 22:37
 */

namespace PeekAndPoke\Component\PropertyAccess;

/**
 * ClassMirror
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ClassMirror
{
    /** @var PropertyAccessFactoryImpl */
    private $factory;

    /**
     */
    public function __construct()
    {
        $this->factory = new PropertyAccessFactoryImpl();
    }

    /**
     * @param mixed $subject
     *
     * @return PropertyAccess[]
     */
    public function getAccessors($subject)
    {
        $class = $subject instanceof \ReflectionClass ? $subject : new \ReflectionClass($subject);

        $result  = [];
        $current = $class;

        while ($current) {

            foreach ($current->getProperties() as $property) {

                $propertyName = $property->getName();

                if (false === isset($result[$propertyName]) && false === $property->isStatic()) {
                    $result[$propertyName] = $this->factory->create($class, $property);
                }
            }
            $current = $current->getParentClass();
        }

        return $result;
    }
}
