<?php
/**
 * File was created 05.10.2015 17:01
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber\Store;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Slumber\Annotation\PropertyPreSaveVisitorMarker;
use PeekAndPoke\Component\Slumber\Annotation\SlumberAnnotation;
use Psr\Container\ContainerInterface;

/**
 * @Annotation
 * @Annotation\Target("PROPERTY")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AsCreatedAt extends SlumberAnnotation implements PropertyPreSaveVisitorMarker
{
    /**
     * @param ContainerInterface  $provider
     * @param mixed               $subject
     * @param \ReflectionProperty $property
     */
    public function onPreSave(ContainerInterface $provider, $subject, \ReflectionProperty $property)
    {
        if ($property->getValue($subject) === null) {
            $property->setValue($subject, new \DateTime());
        }
    }
}
