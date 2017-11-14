<?php
/**
 * File was created 19.03.2015 08:39
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
namespace PeekAndPoke\Component\Emitter;

/**
 * Emitter
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class EmitterImpl implements Emitter
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
    public function bind($eventName, $handler)
    {
        if (false === array_key_exists($eventName, $this->bindings)) {
            $this->bindings[$eventName] = new \SplObjectStorage();
        }

        $listeners = $this->bindings[$eventName];

        if (! $listeners->offsetExists($handler)) {
            $listeners->offsetSet($handler);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function emit(Event $event)
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

            if ($listener instanceof \Closure ||
                $listener instanceof Listener ||
                is_callable($listener)) {

                $listener($event);
            }

            // TODO: check if event has been canceled
        }

        return $this;
    }
}
