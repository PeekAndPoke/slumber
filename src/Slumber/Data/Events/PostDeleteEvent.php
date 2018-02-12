<?php
/**
 * File was created 28.04.2016 07:28
 */

namespace PeekAndPoke\Component\Slumber\Data\Events;

use PeekAndPoke\Component\Emitter\Event;
use Psr\Container\ContainerInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class PostDeleteEvent implements Event
{
    public const NAME = 'POST_DELETE';

    /** @var ContainerInterface */
    private $provider;
    /** @var mixed */
    private $payload;

    /**
     * PostSaveClass constructor.
     *
     * @param ContainerInterface $provider
     * @param mixed              $payload
     */
    public function __construct(ContainerInterface $provider, $payload)
    {
        $this->provider = $provider;
        $this->payload  = $payload;
    }

    /**
     * @return string
     */
    public function getEventName()
    {
        return static::NAME;
    }

    /**
     * @return ContainerInterface
     */
    public function getProvider()
    {
        return $this->provider;
    }

    /**
     * @return mixed
     */
    public function getPayload()
    {
        return $this->payload;
    }
}
