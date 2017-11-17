<?php
/**
 * File was created 07.10.2015 06:33
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec;

use PeekAndPoke\Component\Slumber\Core\LookUp\EntityConfigReader;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class GenericSlumberer implements Slumberer
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
     * @param mixed $subject
     *
     * @return array|mixed|null
     */
    public function slumber($subject)
    {
        if (is_object($subject) &&
            $config = $this->entityConfigLookUp->getEntityConfig(new \ReflectionClass($subject))
        ) {

            $result  = [];
            $entries = $config->getMarkedProperties();

            foreach ($entries as $entry) {

                // put the value to sleep using the alias name
                $result[$entry->alias] = $entry->mapper->slumber(
                    $this,
                    $entry->propertyAccess->get($subject)
                );
            }

            return $result;
        }

        return null;
    }
}
