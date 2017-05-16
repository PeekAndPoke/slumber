<?php
/**
 * File was created 30.09.2015 09:44
 */

namespace PeekAndPoke\Component\Slumber\Annotation;

use Psr\Container\ContainerInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface PropertyPostSaveVisitorMarker extends PropertyMarker
{
    /**
     * @param ContainerInterface  $serviceProvider
     * @param mixed               $subject
     * @param \ReflectionProperty $property
     * @param mixed               $returnedData Data returned from storage
     */
    public function onPostCreate(
        ContainerInterface $serviceProvider,
        $subject,
        \ReflectionProperty $property,
        $returnedData
    );
}
