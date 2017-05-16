<?php
/**
 * File was created 19.03.2015 08:33
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
namespace PeekAndPoke\Component\Emitter\Interfaces;

/**
 * EmitterInterface
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface EmitterInterface
{
    /**
     * @param string                     $eventName
     * @param ListenerInterface|\Closure $eventHandler
     *
     * @return $this
     */
    public function bind($eventName, $eventHandler);

    /**
     * @param EventInterface $event
     *
     * @return $this
     */
    public function emit(EventInterface $event);
}
