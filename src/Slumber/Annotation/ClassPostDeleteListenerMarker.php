<?php
/**
 * File was created 28.04.2016 07:18
 */

namespace PeekAndPoke\Component\Slumber\Annotation;

use PeekAndPoke\Component\Slumber\Data\Events\PostDeleteEvent;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface ClassPostDeleteListenerMarker extends ClassMarker
{
    /**
     * @param PostDeleteEvent $event
     */
    public function execute(PostDeleteEvent $event);
}
