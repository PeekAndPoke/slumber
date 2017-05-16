<?php
/**
 * File was created 06.10.2015 06:21
 */

namespace PeekAndPoke\Component\Slumber\Core\LookUp;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface EntityConfigReader
{
    /**
     * @param \ReflectionClass $subject
     *
     * @return EntityConfig
     */
    public function getEntityConfig(\ReflectionClass $subject);
}
