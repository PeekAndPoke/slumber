<?php
/**
 * File was created 28.04.2016 06:48
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber\Store;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Slumber\Annotation\ClassPostSaveListenerMarker;
use PeekAndPoke\Component\Slumber\Annotation\ServiceInjectingSlumberAnnotation;
use PeekAndPoke\Component\Slumber\Data\Events\PostSaveEvent;
use PeekAndPoke\Component\Slumber\Data\Journal\JournalWriter;
use PeekAndPoke\Component\Slumber\SlumberDependencies;

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
        return SlumberDependencies::JOURNAL_WRITER;
    }

    /**
     * @return string
     */
    public function getServiceClassDefinitionDefault()
    {
        return SlumberDependencies::JOURNAL_WRITER_CLASS;
    }
}
