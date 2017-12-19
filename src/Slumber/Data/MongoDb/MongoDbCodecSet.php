<?php
/**
 * File was created 09.10.2015 13:26
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use PeekAndPoke\Component\Slumber\Data\EntityPool;
use PeekAndPoke\Component\Slumber\Data\Events\PostDeleteEvent;
use PeekAndPoke\Component\Slumber\Data\Events\PostSaveEvent;
use PeekAndPoke\Component\Slumber\Data\Storage;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MongoDbCodecSet
{
    /** @var ContainerInterface */
    private $container;
    /** @var MongoDbEntityConfigReader */
    private $lookUp;
    /** @var MongoDbSlumberer */
    private $slumberer;
    /** @var MongoDbAwaker */
    private $awaker;
    /** @var MongoDbIndexer */
    private $indexer;

    /**
     * @param ContainerInterface        $container
     * @param MongoDbEntityConfigReader $lookUp
     * @param EntityPool                $pool
     * @param Storage                   $storage
     * @param LoggerInterface           $logger
     */
    public function __construct(ContainerInterface $container, MongoDbEntityConfigReader $lookUp, EntityPool $pool, Storage $storage, LoggerInterface $logger)
    {
        $this->lookUp = $lookUp;

        $this->slumberer = new MongoDbSlumbererImpl($lookUp, $storage, $container);

        $this->awaker = new MongoDbPoolingAwaker(
            new MongoDbAwakerImpl($lookUp, $storage), $pool, $lookUp
        );

        $this->container = $container;

        $this->indexer = new MongoDbIndexer($lookUp, $logger);
    }

    /**
     * @return MongoDbEntityConfigReader
     */
    public function getLookUp()
    {
        return $this->lookUp;
    }

    /**
     * @param mixed $subject
     * @param mixed $slumbering
     *
     * @return PostSaveEvent
     */
    public function createPostSaveEventFor($subject, $slumbering)
    {
        return new PostSaveEvent($this->container, $subject, $slumbering);
    }

    /**
     * @param mixed $subject
     *
     * @return PostDeleteEvent
     */
    public function createPostDeleteEventFor($subject)
    {
        return new PostDeleteEvent($this->container, $subject);
    }

    /**
     * @return MongoDbSlumberer
     */
    public function getSlumberer()
    {
        return $this->slumberer;
    }

    /**
     * @return MongoDbAwaker
     */
    public function getAwaker()
    {
        return $this->awaker;
    }

    /**
     * @return MongoDbIndexer
     */
    public function getIndexer()
    {
        return $this->indexer;
    }
}
