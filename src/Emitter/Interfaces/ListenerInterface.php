<?php
/**
 * File was created 19.03.2015 08:34
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
namespace PeekAndPoke\Component\Emitter\Interfaces;

/**
 * ListenerInterface
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface ListenerInterface
{
    /**
     * @param EventInterface $event
     */
    public function __invoke($event);
}
