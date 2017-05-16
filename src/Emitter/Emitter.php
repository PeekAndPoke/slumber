<?php
/**
 * File was created 19.03.2015 08:39
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
namespace PeekAndPoke\Component\Emitter;

use PeekAndPoke\Component\Emitter\Interfaces\EmitterInterface;
use PeekAndPoke\Component\Emitter\Interfaces\EventInterface;
use PeekAndPoke\Component\Emitter\Interfaces\ListenerInterface;

/**
 * Emitter
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class Emitter implements EmitterInterface
{
    /** @var bool */
    private $enabled = true;

    /** @var \SplObjectStorage[] */
    private $bindings = [];

    /**
     * @param bool $enabled
     */
    public function enable($enabled = true)
    {
        $this->enabled = $enabled;
    }

    /**
     * {@inheritdoc}
     */
    public function bind($eventName, $eventHandler)
    {
        if (false === array_key_exists($eventName, $this->bindings)) {
            $this->bindings[$eventName] = new \SplObjectStorage();
        }

        $listeners = $this->bindings[$eventName];

        if (! $listeners->offsetExists($eventHandler)) {
            $listeners->offsetSet($eventHandler);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function emit(EventInterface $event)
    {
        if ($this->enabled === false) {
            return $this;
        }

        $eventName = $event->getEventName();

        if (false === array_key_exists($eventName, $this->bindings)) {
            return $this;
        }

        $listeners = $this->bindings[$eventName];

        foreach ($listeners as $listener) {

            if ($listener instanceof \Closure) {
                $listener($event);
            }

            if ($listener instanceof ListenerInterface) {
                $listener->__invoke($event);
            }

            // TODO: check if event has been canceled
        }

        return $this;
    }
}
