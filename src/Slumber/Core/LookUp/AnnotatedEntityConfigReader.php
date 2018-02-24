<?php
/**
 * File was created 06.10.2015 06:22
 */

namespace PeekAndPoke\Component\Slumber\Core\LookUp;

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\Reader;
use PeekAndPoke\Component\Creator\Creator;
use PeekAndPoke\Component\Creator\CreatorFactory;
use PeekAndPoke\Component\Creator\CreatorFactoryImpl;
use PeekAndPoke\Component\PropertyAccess\PropertyAccessFactory;
use PeekAndPoke\Component\PropertyAccess\PropertyAccessFactoryImpl;
use PeekAndPoke\Component\Psi\Psi;
use PeekAndPoke\Component\Slumber\Annotation\ClassCreatorMarker;
use PeekAndPoke\Component\Slumber\Annotation\ClassMarker;
use PeekAndPoke\Component\Slumber\Annotation\PropertyMappingMarker;
use PeekAndPoke\Component\Slumber\Annotation\PropertyMarker;
use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;
use PeekAndPoke\Component\Slumber\Core\Validation\ClassAnnotationValidationContext;
use PeekAndPoke\Component\Slumber\Core\Validation\PropertyAnnotationValidationContext;
use Psr\Container\ContainerInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AnnotatedEntityConfigReader implements EntityConfigReader
{
    /** @var Reader */
    private $annotationReader;
    /** @var ContainerInterface */
    private $serviceProvider;
    /** @var PropertyMarker2Mapper */
    private $mappings;
    /** @var CreatorFactory */
    private $creatorFactory;
    /** @var PropertyAccessFactory */
    private $propertyAccessFactory;

    /**
     * @param ContainerInterface    $serviceProvider
     * @param Reader                $annotationReader
     * @param PropertyMarker2Mapper $mappings
     */
    public function __construct(ContainerInterface $serviceProvider, Reader $annotationReader, PropertyMarker2Mapper $mappings)
    {
        static $autoloader = false;

        if ($autoloader === false) {
            $autoloader = true;
            AnnotationRegistry::registerUniqueLoader('class_exists');
        }

        $this->annotationReader = $annotationReader;
        $this->serviceProvider  = $serviceProvider;
        $this->mappings         = $mappings;

        $this->creatorFactory        = new CreatorFactoryImpl();
        $this->propertyAccessFactory = new PropertyAccessFactoryImpl();
    }

    /**
     * @param \ReflectionClass $subject
     *
     * @return EntityConfig
     * @throws SlumberException
     */
    public function getEntityConfig(\ReflectionClass $subject)
    {
        $config = new EntityConfig(
            $subject->name,
            $this->getCreator($subject),
            $this->getClassMarkers($subject),
            $this->getPropertyMarkersRecursive($subject)
        );

        return $config;
    }

    /**
     * @param PropertyMarkedForSlumber $marked
     *
     * @return PropertyMarkedForSlumber
     */
    protected function enrich(PropertyMarkedForSlumber $marked)
    {
        $marked->mapper = $this->mappings->createMapper($marked->marker);

        return $marked;
    }

    /**
     * @param \ReflectionClass $subject
     *
     * @return Creator
     */
    private function getCreator(\ReflectionClass $subject) : Creator
    {
        $validationContext = new ClassAnnotationValidationContext($this->serviceProvider, $subject);

        return Psi::it($this->annotationReader->getClassAnnotations($subject))
            ->filter(new Psi\IsInstanceOf(ClassCreatorMarker::class))
            ->each($validationContext)
            // map the first one to a Creator
            ->map(function (ClassCreatorMarker $marker) { return $marker->getCreator($this->creatorFactory); })
            // or get the default creator
            ->getFirst($this->creatorFactory->create($subject));
    }

    /**
     * @param \ReflectionClass $subject
     *
     * @return ClassMarker[]
     */
    private function getClassMarkers(\ReflectionClass $subject) : array
    {
        $validationContext = new ClassAnnotationValidationContext($this->serviceProvider, $subject);

        return Psi::it($this->annotationReader->getClassAnnotations($subject))
            ->filter(new Psi\IsInstanceOf(ClassMarker::class))
            ->each($validationContext)
            ->toArray();
    }

    /**
     * @param \ReflectionClass $subject
     *
     * @return PropertyMarkedForSlumber[]
     */
    private function getPropertyMarkersRecursive(\ReflectionClass $subject) : array
    {
        /** @var PropertyMarkedForSlumber[] $result */
        $result = [];

        $base = $subject;

        // We climb up the inheritance ladder. Be doing so we can also capture private properties of base classes
        // that are NOT shadowed by the inheriting classes.
        while ($base instanceof \ReflectionClass && $base->isUserDefined()) {

            $this->getPropertyMarkersForClass($subject, $base, $result);

            $base = $base->getParentClass();
        }

        return array_values($result);
    }

    private function getPropertyMarkersForClass(\ReflectionClass $subject, \ReflectionClass $base, array &$result) : void
    {
        $properties = $base->getProperties();

        foreach ($properties as $property) {

            $propertyName = $property->getName();

            if (! isset($result[$propertyName])) {

                $marker = $this->getPropertyAnnotationsOfType($subject, $base, $property);

                if ($marker) {
                    $result[$propertyName] = $this->enrich($marker);
                }
            }
        }
    }

    /**
     * @param \ReflectionClass    $cls
     * @param \ReflectionProperty $property
     *
     * @return PropertyAnnotationValidationContext
     */
    private function getPropertyValidationContext(\ReflectionClass $cls, \ReflectionProperty $property)
    {
        return new PropertyAnnotationValidationContext($this->serviceProvider, $cls, $property);
    }

    /**
     * @param \ReflectionClass    $subject
     * @param \ReflectionClass    $base
     * @param \ReflectionProperty $property
     *
     * @return null|PropertyMarkedForSlumber
     */
    private function getPropertyAnnotationsOfType(\ReflectionClass $subject, \ReflectionClass $base, \ReflectionProperty $property) : ?PropertyMarkedForSlumber
    {
        $annotations = $this->annotationReader->getPropertyAnnotations($property);

        // get all slumber marker annotations
        $allMarkers = Psi::it($annotations)
            ->filter(new Psi\IsInstanceOf(PropertyMarker::class))
            ->each($this->getPropertyValidationContext($base, $property))
            ->toArray();

        // get the FIRST mapping marker like AsString() or AsObject() ...
        $mappingMarker = Psi::it($allMarkers)
            ->filter(new Psi\IsInstanceOf(PropertyMappingMarker::class))
            ->getFirst();

        if ($mappingMarker === null) {
            return null;
        }

        $newEntry = new PropertyMarkedForSlumber();

        $newEntry->name       = $property->getName();
        $newEntry->alias      = $mappingMarker->hasAlias() ? $mappingMarker->getAlias() : $property->getName();
        $newEntry->marker     = $mappingMarker;
        $newEntry->allMarkers = $allMarkers;
        $newEntry->mapper     = $this->mappings->createMapper($mappingMarker);

        // We need to create the property access with the main subject class in mind.
        // By doing so we can access private properties of base classes.
        $newEntry->propertyAccess = $this->propertyAccessFactory->create($subject, $property);

        return $newEntry;
    }
}
