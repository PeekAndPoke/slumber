<?php
/**
 * File was created 14.02.2016 00:04
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use PeekAndPoke\Component\Slumber\Core\Codec\GenericSlumberer;
use PeekAndPoke\Component\Slumber\Data\Storage;
use Psr\Container\ContainerInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MongoDbSlumbererImpl extends GenericSlumberer implements MongoDbSlumberer
{
    /** @var Storage */
    protected $storage;
    /** @var ContainerInterface */
    private $serviceProvider;

    /**
     * @param MongoDbEntityConfigReader $lookUp
     * @param Storage                   $storage
     * @param ContainerInterface        $serviceProvider
     */
    public function __construct(MongoDbEntityConfigReader $lookUp, Storage $storage, ContainerInterface $serviceProvider)
    {
        parent::__construct($lookUp);

        $this->storage         = $storage;
        $this->serviceProvider = $serviceProvider;
    }

    /**
     * @return Storage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param mixed $subject
     *
     * @return array|mixed|null
     */
    public function slumber($subject)
    {
        ////  do the pre-create property visits  ///////////////////////////////////////////////////////////////////////

        /** @var MongoDbEntityConfigReader $lookUp */
        $lookUp = $this->entityConfigLookUp;

        $reflect = new \ReflectionClass($subject);
        $config  = $lookUp->getEntityConfig($reflect);
        $visits  = $config->getPreSaveVisits();

        foreach ($visits as $visit) {

            $property = $reflect->getProperty($visit->propertyName);
            $property->setAccessible(true);

            foreach ($visit->markers as $marker) {
                $marker->onPreSave($this->serviceProvider, $subject, $property);
            }
        }

        ////  do the real slumbering  //////////////////////////////////////////////////////////////////////////////////

        return parent::slumber($subject);
    }

}
