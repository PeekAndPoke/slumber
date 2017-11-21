<?php
/**
 * File was created 08.10.2015 18:17
 */

namespace PeekAndPoke\Component\Slumber\Core\Validation;

use PeekAndPoke\Component\Psi\Interfaces\BinaryFunction;
use PeekAndPoke\Component\Slumber\Annotation\SlumberMarker;
use Psr\Container\ContainerInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class PropertyAnnotationValidationContext implements ValidationContext, BinaryFunction
{
    /** @var ContainerInterface */
    public $provider;
    /** @var \ReflectionClass */
    public $cls;
    /** @var \ReflectionProperty */
    public $property;

    /**
     * @param ContainerInterface  $provider
     * @param \ReflectionClass    $cls
     * @param \ReflectionProperty $property
     */
    public function __construct(ContainerInterface $provider, \ReflectionClass $cls, \ReflectionProperty $property)
    {
        $this->provider = $provider;
        $this->cls      = $cls;
        $this->property = $property;
    }

    /**
     * @return ContainerInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @return string
     */
    public function getAnnotationLocation()
    {
        return $this->cls->name . '::$' . $this->property->getName();
    }

    /**
     * @param SlumberMarker $marker
     * @param int           $idx
     *
     * @return void
     */
    public function __invoke($marker, $idx)
    {
        $marker->validate($this);
    }
}
