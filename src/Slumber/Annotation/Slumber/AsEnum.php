<?php
/**
 * File was created 30.09.2015 07:57
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;
use PeekAndPoke\Component\Slumber\Core\Validation\PropertyAnnotationValidationContext;
use PeekAndPoke\Types\Enumerated;

/**
 * @Annotation
 * @Annotation\Target("PROPERTY")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AsEnum extends PropertyMappingMarkerBase
{
    /**
     * @param PropertyAnnotationValidationContext $context
     *
     * @throws SlumberException
     */
    public function validate($context) : void
    {
        if (empty($this->value)) {
            throw $this->createValidationException(
                $context,
                'you must provide a class as value. Example @Slumber\AsObject( MyEnum::class )'
            );
        }

        if (!class_exists($this->value)) {
            throw $this->createValidationException(
                $context,
                "you provided the non-existing class '$this->value'"
            );
        }

        if (!is_a($this->value, Enumerated::class, true)) {
            throw $this->createValidationException(
                $context,
                "you provided the class '$this->value' which does not extend " . Enumerated::class
            );
        }
    }

    /**
     * @return bool
     */
    public function keepNullValuesInCollections() : bool
    {
        return false;
    }
}
