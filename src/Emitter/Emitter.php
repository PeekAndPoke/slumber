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
     */
    public function bind($eventName, $handler);

    /**
     * @param Event $event
     *
     * @return $this
     */
    public function emit(Event $event);
}
