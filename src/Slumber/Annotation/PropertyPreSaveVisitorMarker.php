<?php
/**
 * File was created 30.09.2015 09:44
 */

namespace PeekAndPoke\Component\Slumber\Annotation;

use Psr\Container\ContainerInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface PropertyPreSaveVisitorMarker extends PropertyMarker
{
    /**
     * @param ContainerInterface  $serviceProvider
     * @param mixed               $subject
     * @param \ReflectionProperty $property
     */
    public function onPreSave(ContainerInterface $serviceProvider, $subject, \ReflectionProperty $property);
}
