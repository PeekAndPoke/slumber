<?php
/**
 * File was created 10.10.2015 18:11
 */

namespace PeekAndPoke\Component\Slumber\Core\LookUp;

use PeekAndPoke\Component\Creator\Creator;
use PeekAndPoke\Component\Slumber\Annotation\ClassMarker;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class EntityConfig
{
    /** @var string */
    protected $className;
    /** @var Creator */
    protected $creator;
    /** @var ClassMarker[] */
    protected $markersOnClass;
    /** @var PropertyMarkedForSlumber[] */
    protected $markedProperties;

    /**
     * @param string                     $className
     * @param Creator                    $creator
     * @param ClassMarker[]              $markersOnClass
     * @param PropertyMarkedForSlumber[] $markedProperties
     */
    public function __construct($className, Creator $creator, $markersOnClass, $markedProperties)
    {
        $this->className        = $className;
        $this->creator          = $creator;
        $this->markersOnClass   = $markersOnClass;
        $this->markedProperties = $markedProperties;
    }

    /**
     * @return string
     */
    public function getClassName()
    {
        return $this->className;
    }

    /**
     * @return Creator
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * @return ClassMarker[]
     */
    public function getMarkersOnClass()
    {
        return $this->markersOnClass;
    }

    /**
     * @return PropertyMarkedForSlumber[]
     */
    public function getMarkedProperties()
    {
        return $this->markedProperties;
    }

    /**
     * @param string $name
     *
     * @return null|PropertyMarkedForSlumber
     */
    public function getMarkedPropertyByName($name)
    {
        foreach ($this->markedProperties as $property) {
            if ($property->name === $name) {
                return $property;
            }
        }

        return null;
    }

    public function warmUp()
    {
        $reflect = new \ReflectionClass($this->className);

        foreach ($this->markedProperties as $markedProperty) {
            // set up the reflection property for later use, so we only need to do it once
            $prop = $reflect->getProperty($markedProperty->name);
            $prop->setAccessible(true);

            $markedProperty->reflectionProperty = $prop;
        }

//        foreach ($this->markedProperties as $markedProperty) {
//            $markedProperty->reflectionProperty = new PropertyAccess($markedProperty->name);
//        }
    }
}
