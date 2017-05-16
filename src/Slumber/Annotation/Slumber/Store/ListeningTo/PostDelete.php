<?php

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber\Store\ListeningTo;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Slumber\Annotation\ClassPostDeleteListenerMarker;
use PeekAndPoke\Component\Slumber\Data\Events\PostDeleteEvent;

/**
 * @Annotation
 * @Annotation\Target("CLASS")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class PostDelete extends AbstractListenerMarker implements ClassPostDeleteListenerMarker
{
    /**
     * @param PostDeleteEvent $event
     */
    public function execute(PostDeleteEvent $event)
    {
        $this->getService($event->getProvider())->__invoke($event);
    }
}
