<?php
/**
 * File was created 11.02.2016 06:06
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber\Store;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Slumber\Annotation\PropertyStorageMarker;
use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;

/**
 * Id defines the primary identifier in an entity held in a storage like MongoDb
 *
 * @Annotation
 * @Annotation\Target("PROPERTY")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AsId implements PropertyStorageMarker
{
    /**
     * Initialize the annotation and validate the given parameters
     *
     * @param mixed $context
     *
     * @throws SlumberException
     */
    public function validate($context)
    {
        // noop
    }
}
