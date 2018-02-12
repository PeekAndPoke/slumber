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
class PostSaveEvent implements Event
{
    public const NAME = 'POST_SAVE';

    /** @var ContainerInterface */
    private $provider;
    /** @var mixed */
    private $payload;
    /** @var array */
    private $slumberingData;

    /**
     * PostSaveClass constructor.
     *
     * @param ContainerInterface $provider
     * @param mixed              $payload
     * @param array              $slumberingData
     */
    public function __construct(ContainerInterface $provider, $payload, $slumberingData)
    {
        $this->provider       = $provider;
        $this->payload        = $payload;
        $this->slumberingData = $slumberingData;
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

    /**
     * @return array
     */
    public function getSlumberingData()
    {
        return $this->slumberingData;
    }
}
