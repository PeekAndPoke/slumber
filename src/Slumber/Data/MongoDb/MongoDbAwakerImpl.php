<?php
/**
 * File was created 03.03.2016 07:13
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use PeekAndPoke\Component\Slumber\Core\Codec\GenericAwaker;
use PeekAndPoke\Component\Slumber\Core\LookUp\EntityConfigReader;
use PeekAndPoke\Component\Slumber\Data\Storage;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MongoDbAwakerImpl extends GenericAwaker implements MongoDbAwaker
{
    /** @var Storage */
    private $storage;

    /**
     * MongoDbAwaker constructor.
     *
     * @param EntityConfigReader $entityConfigLookUp
     * @param Storage            $storage
     */
    public function __construct(EntityConfigReader $entityConfigLookUp, Storage $storage)
    {
        parent::__construct($entityConfigLookUp);

        $this->storage = $storage;
    }

    /**
     * @return Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }
}
