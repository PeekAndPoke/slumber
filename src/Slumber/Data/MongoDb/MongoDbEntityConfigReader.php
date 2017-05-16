<?php
/**
 * File was created 11.02.2016 22:13
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use PeekAndPoke\Component\Slumber\Core\LookUp\EntityConfigReader;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface MongoDbEntityConfigReader extends EntityConfigReader
{
    /**
     * @param \ReflectionClass $subject
     *
     * @return MongoDbEntityConfig
     */
    public function getEntityConfig(\ReflectionClass $subject);
}
