<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 12.04.17
 * Time: 06:26
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use MongoDB\Client;
use PeekAndPoke\Component\Slumber\Data\EntityPool;
use PeekAndPoke\Component\Slumber\Data\StorageDriver;
use PeekAndPoke\Component\Slumber\Data\StorageDriverFactory;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MongoDbStorageDriverFactory implements StorageDriverFactory
{
    /** @var EntityPool */
    private $entityPool;
    /** @var MongoDbCodecSet */
    private $codecSet;

    /** @var Client[] Key is the dns, value is the Client */
    private $dns2client = [];

    /**
     * MongoDbStorageDriverFactory constructor.
     *
     * @param EntityPool      $entityPool
     * @param MongoDbCodecSet $codecSet
     */
    public function __construct(EntityPool $entityPool, MongoDbCodecSet $codecSet)
    {
        $this->entityPool = $entityPool;
        $this->codecSet   = $codecSet;
    }

    /**
     * @return Client[]
     */
    public function getDns2client()
    {
        return $this->dns2client;
    }

    /**
     * @param array            $config
     * @param string           $tableName
     * @param \ReflectionClass $baseClass
     *
     * @return StorageDriver|MongoDbStorageDriver
     */
    public function create($config, $tableName, \ReflectionClass $baseClass)
    {
        $client = $this->getOrCreateClient($config['dns']);
        $table  = $client->selectCollection($config['database'], $tableName);

        return new MongoDbStorageDriver($this->entityPool, $this->codecSet, $table, $baseClass);
    }

    /**
     * @param string $dns
     *
     * @return Client
     */
    private function getOrCreateClient($dns)
    {
        if (isset($this->dns2client[$dns])) {
            return $this->dns2client[$dns];
        }

        return $this->dns2client[$dns] = new Client(
            $dns,
            [
                // do not connect right away
                'connect' => false,
                // force return of associative array
                'typeMap' => ['root' => 'array', 'document' => 'array', 'array' => 'array'],
            ]
        );
    }
}
