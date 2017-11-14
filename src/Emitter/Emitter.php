<?php
/**
 * File was created 19.03.2015 08:33
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */

namespace PeekAndPoke\Component\Emitter;

use PeekAndPoke\Component\Psi\Interfaces\Functions\UnaryFunctionInterface;

/**
 * Emitter
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface Emitter
{
    /**
     * @param string                                   $eventName
     * @param Listener|UnaryFunctionInterface|callable $handler
     *
     * @return $this
     *
     * @throws \LogicException When the eventName is empty or the handler is not correct
     */
    public function bind($eventName, $handler);

    /**
     * @param Event $event
     *
     * @return $this
     */
    public function emit(Event $event);
}
