<?php
/**
 * File was created 19.03.2015 08:34
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
namespace PeekAndPoke\Component\Emitter;

/**
 * Listener for an emitter
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface Listener
{
    /**
     * @param Event $event
     */
    public function __invoke($event);
}
