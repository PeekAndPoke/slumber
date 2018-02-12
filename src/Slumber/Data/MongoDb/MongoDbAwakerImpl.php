<?php
/**
 * File was created 03.03.2016 07:13
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use MongoDB\BSON;
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

    public function awake($data, \ReflectionClass $cls)
    {
        //
        // The MongoDB driver returns some typed things that we can normalize here:
        // - \MongoDB\BSON\UTCDateTime
        // - \MongoDB\BSON\ObjectID
        //
        if (\is_array($data)) {
            array_walk_recursive($data, function (&$item) {

                /** @noinspection ReferenceMismatchInspection */
                if ($item === null ||
                    is_scalar($item) ||
                    \is_array($item)) {
                    return;
                }

                if ($item instanceof BSON\UTCDateTime) {
                    $item = $item->toDateTime();

                    return;
                }

                if ($item instanceof BSON\ObjectID) {
                    $item = $item->__toString();

                    return;
                }
            });
        }

        return parent::awake($data, $cls);
    }
}
