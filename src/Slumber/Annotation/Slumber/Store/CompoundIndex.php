<?php
/**
 * File was created 01.03.2016 12:07
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber\Store;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Slumber\Annotation\ClassMarker;
use PeekAndPoke\Component\Slumber\Annotation\CompoundIndexDefinition;
use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;
use PeekAndPoke\Component\Slumber\Core\Validation\ClassAnnotationValidationContext;

/**
 * @Annotation
 * @Annotation\Target("CLASS")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class CompoundIndex extends AbstractIndexDefinition implements ClassMarker, CompoundIndexDefinition
{
    /**
     * @var array
     *
     * @Annotation\Required()
     */
    public $def = [];

    /**
     * @param ClassAnnotationValidationContext $context
     *
     * @throws SlumberException
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

        if (! \is_array($this->def)) {
            throw $this->createValidationException(
                $context,
                'The compound index definition must be defined as a key value pair. Example: { \'one\': 1, \'two\': -1 }'
            );
        }
    }

    /**
     * @return array
     */
    public function getDefinition()
    {
        return $this->def;
    }
}
