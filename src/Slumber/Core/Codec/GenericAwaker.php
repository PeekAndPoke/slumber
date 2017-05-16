<?php
/**
 * File was created 07.10.2015 06:35
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec;

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
        // create an instance
        $subject = $config->getCreator()->create($data);

        // if we do not get an instance we return nothing
        if ($subject === null) {
            return null;
        }

        // NOTICE: creating the instance can change the type (e.g. for polymorphic mapping)
        //         Therefore we need to read the config again if the type has changed
        if ($cls->getName() !== get_class($subject)) {
            $config = $this->entityConfigLookUp->getEntityConfig(new \ReflectionClass($subject));
        }

        // get the properties we need to map
        $entries = $config->getMarkedProperties();

        foreach ($entries as $entry) {
            // awake using the alias name
            $alias = $entry->alias;

            // do we have data for that property
            if (isset($data[$alias])) {
                $entry->reflectionProperty->setValue(
                    $subject,
                    $entry->mapper->awake($this, $data[$alias])
                );
            }
        }

        return $subject;
    }
}
