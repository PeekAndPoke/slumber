<?php
/**
 * File was created 22.06.2016 22:53
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber\Store\ListeningTo;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Slumber\Annotation\ClassPostSaveListenerMarker;
use PeekAndPoke\Component\Slumber\Data\Events\PostSaveEvent;

/**
 * @Annotation
 * @Annotation\Target("CLASS")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class PostSave extends AbstractListenerMarker implements ClassPostSaveListenerMarker
{
    /**
     * @param PostSaveEvent $event
     */
    public function execute(PostSaveEvent $event)
    {
        $this->getService($event->getProvider())->__invoke($event);
    }
}
