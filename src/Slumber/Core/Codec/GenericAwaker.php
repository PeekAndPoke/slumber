<?php
/**
 * File was created 07.10.2015 06:35
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec;

use PeekAndPoke\Component\Slumber\Core\LookUp\EntityConfig;
use PeekAndPoke\Component\Slumber\Core\LookUp\EntityConfigReader;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class GenericAwaker implements Awaker
{
    /** @var EntityConfigReader */
    protected $entityConfigLookUp;

    /**
     * @param EntityConfigReader $entityConfigLookUp
     */
    public function __construct(EntityConfigReader $entityConfigLookUp)
    {
        $this->entityConfigLookUp = $entityConfigLookUp;
    }

    /**
     * @param mixed            $data
     * @param \ReflectionClass $cls
     *
     * @return mixed|null
     */
    public function awake($data, \ReflectionClass $cls)
    {
        // read the config for the given class
        $config = $this->entityConfigLookUp->getEntityConfig($cls);

        if ($config === null) {
            return null;
        }

        // create an instance
        $subject = $config->getCreator()->create($data);

        // if we do not get an instance we return nothing
        if ($subject === null) {
            return null;
        }

        // NOTICE: creating the instance can change the type (e.g. for polymorphic mapping)
        //         Therefore we need to read the config again if the type has changed
        if ($cls->name !== get_class($subject)) {
            $config = $this->entityConfigLookUp->getEntityConfig(new \ReflectionClass($subject));
        }

        // populate the result
        $this->populate($subject, $data, $config);

        return $subject;
    }

    private function populate($subject, $data, EntityConfig $config)
    {
        // get the properties we need to map
        $entries = $config->getMarkedProperties();

        foreach ($entries as $entry) {
            // awake using the alias name
            $alias = $entry->alias;

            // do we have data for that property
            if (isset($data[$alias])) {
                $entry->propertyAccess->set(
                    $subject,
                    $entry->mapper->awake($this, $data[$alias])
                );
            }
        }
    }
}
