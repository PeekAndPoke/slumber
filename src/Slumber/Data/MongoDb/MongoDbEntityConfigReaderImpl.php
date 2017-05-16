<?php
/**
 * File was created 11.02.2016 17:26
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use PeekAndPoke\Component\Slumber\Core\LookUp\DelegatingEntityConfigReader;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MongoDbEntityConfigReaderImpl extends DelegatingEntityConfigReader implements MongoDbEntityConfigReader
{
    /**
     * @param \ReflectionClass $subject
     *
     * @return MongoDbEntityConfig
     */
    public function getEntityConfig(\ReflectionClass $subject)
    {
        $parent = $this->delegate->getEntityConfig($subject);

        return MongoDbEntityConfig::from($parent);
    }
}
