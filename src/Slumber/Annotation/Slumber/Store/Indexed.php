<?php
/**
 * File was created 29.02.2016 16:39
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber\Store;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Slumber\Annotation\PropertyStorageIndexMarker;

/**
 * @Annotation
 * @Annotation\Target("PROPERTY")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class Indexed extends AbstractIndexDefinition implements PropertyStorageIndexMarker
{
    /**
     * @inheritdoc
     */
    public function validate($context)
    {
        $direction = $this->getDirection();

        if ($direction !== self::ASCENDING && $direction !== self::DESCENDING) {
            throw $this->createValidationException(
                $context,
                'The index direction must be "ASC" or "DESC" but "' . $direction . '" was found.'
            );
        }
    }
}
