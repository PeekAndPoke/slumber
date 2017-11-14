<?php
/**
 * File was created 28.04.2016 06:48
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber\Store;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Slumber\Annotation\ClassPostSaveListenerMarker;
use PeekAndPoke\Component\Slumber\Annotation\ServiceInjectingSlumberAnnotation;
use PeekAndPoke\Component\Slumber\Data\Addon\Journal\JournalWriter;
use PeekAndPoke\Component\Slumber\Data\Events\PostSaveEvent;

/**
 * @Annotation
 * @Annotation\Target("CLASS")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class Journalized extends ServiceInjectingSlumberAnnotation implements ClassPostSaveListenerMarker
{
    /**
     * @param PostSaveEvent $event
     */
    public function execute(PostSaveEvent $event)
    {
        /** @var JournalWriter $service */
        $service = $this->getService($event->getProvider());

        $service->write($event->getPayload(), $event->getSlumberingData());
    }

    /**
     * @return string
     */
    public function getServiceDefinitionDefault()
    {
        return JournalWriter::SERVICE_ID;
    }

    /**
     * @return string
     */
    public function getServiceClassDefinitionDefault()
    {
        return JournalWriter::class;
    }
}
