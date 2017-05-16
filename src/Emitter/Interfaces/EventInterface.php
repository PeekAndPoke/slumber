<?php
/**
 * File was created 19.03.2015 08:35
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
namespace PeekAndPoke\Component\Emitter\Interfaces;

/**
 * EventInterface
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface EventInterface
{
    /**
     * @return string
     */
    public function getEventName();

    /**
     * @return mixed
     */
    public function getPayload();
}
