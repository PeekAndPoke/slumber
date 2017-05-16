<?php
/**
 * File was created 19.03.2015 08:44
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
namespace PeekAndPoke\Component\Emitter;

use PeekAndPoke\Component\Emitter\Interfaces\EventInterface;

/**
 * Event
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class Event implements EventInterface
{
    /** @var string */
    private $eventName;
    /** @var mixed|null */
    private $payload;

    /**
     * @param string $eventName
     * @param mixed  $payload
     */
    public function __construct($eventName, $payload = null)
    {
        $this->eventName = $eventName;
        $this->payload = $payload;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return $this->eventName;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
