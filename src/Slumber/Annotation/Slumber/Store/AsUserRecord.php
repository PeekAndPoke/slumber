<?php
/**
 * File was created 26.04.2016 07:20
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber\Store;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Slumber\Annotation\PropertyPreSaveVisitorMarker;
use PeekAndPoke\Component\Slumber\Annotation\ServiceInjectingSlumberAnnotation;
use PeekAndPoke\Component\Slumber\Data\Addon\UserRecord\UserRecord;
use PeekAndPoke\Component\Slumber\Data\Addon\UserRecord\UserRecordProvider;
use Psr\Container\ContainerInterface;

/**
 * @Annotation
 * @Annotation\Target("PROPERTY")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AsUserRecord extends ServiceInjectingSlumberAnnotation implements PropertyPreSaveVisitorMarker
{
    /**
     * @param ContainerInterface  $provider
     * @param mixed               $subject
     * @param \ReflectionProperty $property
     */
    public function onPreSave(ContainerInterface $provider, $subject, \ReflectionProperty $property)
    {
        $val = $property->getValue($subject);

        if ($val === null || ($val instanceof UserRecord && empty($val->getName()))) {

            /** @var UserRecordProvider $creator */
            $creator = $this->getService($provider);

            $property->setValue($subject, $creator->getUserRecord());
        }
    }
}
