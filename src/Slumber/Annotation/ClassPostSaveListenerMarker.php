<?php
/**
 * File was created 28.04.2016 07:18
 */

namespace PeekAndPoke\Component\Slumber\Annotation;

use PeekAndPoke\Component\Slumber\Data\Events\PostSaveEvent;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface ClassPostSaveListenerMarker extends ClassMarker
{
    /**
     * @param PostSaveEvent $event
     */
    public function execute(PostSaveEvent $event);
}
