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
     * @var array|string
     *
     * @Annotation\Required()
     */
    public $def;

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

        if ($this->getDefinition() === null) {
            throw $this->createValidationException(
                $context,
                'The compound index definition could not be json decoded. Maybe it is malformed or missing the \'single quotes\' for the keys. ' .
                'Valid example: "{ \'one\': 1, \'two\': -1 }"'
            );
        }
    }

    /**
     * @return string a json string defining the index settings
     */
    public function def()
    {
        return $this->def;
    }

    /**
     * @return array
     */
    public function getDefinition()
    {
        // in case the definition was given as a json within a string
        if (is_string($this->def)) {
            return json_decode(
                str_replace("'", '"', $this->def),
                true
            );
        }

        // otherwise we use what we got
        return $this->def;
    }
}
