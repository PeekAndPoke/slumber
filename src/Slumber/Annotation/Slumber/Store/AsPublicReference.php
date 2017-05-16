<?php
/**
 * File was created 05.10.2015 17:01
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber\Store;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Slumber\Annotation\PropertyPreSaveVisitorMarker;
use PeekAndPoke\Component\Slumber\Annotation\ServiceInjectingSlumberAnnotation;
use PeekAndPoke\Component\Slumber\Data\Addon\PublicReference\PublicReferenceGenerator;
use Psr\Container\ContainerInterface;

/**
 * @Annotation
 * @Annotation\Target("PROPERTY")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AsPublicReference extends ServiceInjectingSlumberAnnotation implements PropertyPreSaveVisitorMarker
{
    /**
     * @param ContainerInterface  $provider
     * @param mixed               $subject
     * @param \ReflectionProperty $property
     */
    public function onPreSave(ContainerInterface $provider, $subject, \ReflectionProperty $property)
    {
        if (empty($property->getValue($subject))) {
            $property->setValue($subject, $this->createReference($provider, $subject));
        }
    }

    /**
     * @param ContainerInterface $provider
     * @param mixed              $subject
     *
     * @return mixed
     */
    private function createReference(ContainerInterface $provider, $subject)
    {
        /** @var PublicReferenceGenerator $creator */
        $creator = $this->getService($provider);

        return $creator->create($subject);
    }
}
