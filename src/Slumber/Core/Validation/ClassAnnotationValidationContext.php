<?php
/**
 * File was created 08.10.2015 18:17
 */

namespace PeekAndPoke\Component\Slumber\Core\Validation;

use PeekAndPoke\Component\Psi\BinaryFunction;
use PeekAndPoke\Component\Slumber\Annotation\SlumberMarker;
use Psr\Container\ContainerInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ClassAnnotationValidationContext implements ValidationContext, BinaryFunction
{
    /** @var ContainerInterface */
    public $provider;
    /** @var \ReflectionClass */
    public $cls;

    /**
     * @param ContainerInterface $provider
     * @param \ReflectionClass   $cls
     */
    public function __construct(ContainerInterface $provider, \ReflectionClass $cls)
    {
        $this->provider = $provider;
        $this->cls      = $cls;
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
        return $this->cls->name;
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
