<?php
/**
 * File was created 06.10.2015 06:22
 */

namespace PeekAndPoke\Component\Slumber\Core\LookUp;

use Doctrine\Common\Annotations\Reader;
use PeekAndPoke\Component\Creator\Creator;
use PeekAndPoke\Component\Creator\CreatorFactory;
use PeekAndPoke\Component\Creator\CreatorFactoryImpl;
use PeekAndPoke\Component\PropertyAccess\PropertyAccessFactory;
use PeekAndPoke\Component\PropertyAccess\PropertyAccessFactoryImpl;
use PeekAndPoke\Component\Psi\Functions\Unary\Matcher\IsInstanceOf;
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
    private function getCreator(\ReflectionClass $subject)
    {
        $validationContext = new ClassAnnotationValidationContext($this->serviceProvider, $subject);

        $creatorAnnotation = Psi::it($this->annotationReader->getClassAnnotations($subject))
            ->filter(new IsInstanceOf(ClassCreatorMarker::class))
            ->each($validationContext)
            ->getFirst();

        if ($creatorAnnotation instanceof ClassCreatorMarker) {
            return $creatorAnnotation->getCreator($this->creatorFactory);
        }

        return $this->creatorFactory->create($subject);
    }

    /**
     * @param \ReflectionClass $subject
     *
     * @return ClassMarker[]
     */
    private function getClassMarkers(\ReflectionClass $subject)
    {
        $validationContext = new ClassAnnotationValidationContext($this->serviceProvider, $subject);

        return Psi::it($this->annotationReader->getClassAnnotations($subject))
            ->filter(new IsInstanceOf(ClassMarker::class))
            ->each($validationContext)
            ->toArray();
    }

    /**
     * @param \ReflectionClass $subjectClass
     *
     * @return PropertyMarkedForSlumber[]
     */
    private function getPropertyMarkersRecursive(\ReflectionClass $subjectClass)
    {
        /** @var PropertyMarkedForSlumber[] $result */
        $result = [];

        // We climb up the inheritance ladder to also capture private properties that are not shadowed
        // by other properties on derived classes.
        while ($subjectClass instanceof \ReflectionClass && $subjectClass->isUserDefined()) {

            $properties = $subjectClass->getProperties();

            foreach ($properties as $property) {

                $propertyName = $property->getName();

                if (!isset($result[$propertyName])) {

                    $context      = $this->getPropertyValidationContext($subjectClass, $property);
                    $marker       = $this->getPropertyAnnotationsOfType($context);

                    if ($marker) {
                        $result[$propertyName] = $this->enrich($marker);
                    }
                }
            }

            $subjectClass = $subjectClass->getParentClass();
        }

        return array_values($result);
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
     * @param PropertyAnnotationValidationContext $context
     *
     * @return PropertyMarkedForSlumber
     */
    private function getPropertyAnnotationsOfType(PropertyAnnotationValidationContext $context)
    {
        $annotations = $this->annotationReader->getPropertyAnnotations($context->property);

        // get the mapping marker like AsString() or AsObject() ...
        $marker = Psi::it($annotations)
            ->filter(new IsInstanceOf(PropertyMappingMarker::class))
            ->each($context)
            ->getFirst();

        if ($marker === null) {
            return null;
        }

        $newEntry = new PropertyMarkedForSlumber();

        $newEntry->name           = $context->property->getName();
        $newEntry->alias          = $marker->hasAlias() ? $marker->getAlias() : $context->property->getName();
        $newEntry->marker         = $marker;
        $newEntry->allMarkers     = Psi::it($annotations)
            ->filter(new IsInstanceOf(PropertyMarker::class))
            ->each($context)
            ->toArray();
        $newEntry->mapper         = $this->mappings->createMapper($marker);
        $newEntry->propertyAccess = $this->propertyAccessFactory->create($context->cls, $context->property);

        return $newEntry;
    }
}
