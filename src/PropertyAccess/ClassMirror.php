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
    /** @var \ReflectionClass */
    private $reflect;
    /** @var PropertyAccessFactoryImpl */
    private $factory;

    /**
     * ClassMirror constructor.
     *
     * @param string $cls
     */
    public function __construct($cls)
    {
        $this->reflect = new \ReflectionClass($cls);
        $this->factory = new PropertyAccessFactoryImpl();
    }

    /**
     * @return PropertyAccess[]
     */
    public function getAccessorsToAllNonStaticProperties()
    {
        $result  = [];
        $current = $this->reflect;

        while ($current) {

            foreach ($current->getProperties() as $property) {

                $propertyName = $property->getName();

                if (false === isset($result[$propertyName]) && false === $property->isStatic()) {
                    $result[$propertyName] = $this->factory->create($this->reflect, $property);
                }
            }
            $current = $current->getParentClass();
        }

        return $result;
    }
}
