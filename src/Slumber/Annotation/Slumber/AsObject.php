<?php
/**
 * File was created 30.09.2015 07:52
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;
use PeekAndPoke\Component\Slumber\Core\Validation\PropertyAnnotationValidationContext;

/**
 * @Annotation
 * @Annotation\Target("PROPERTY")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AsObject extends PropertyMappingMarkerBase
{
    /**
     * @param PropertyAnnotationValidationContext $context
     *
     * @throws SlumberException
     */
    public function validate($context)
    {
        if (empty($this->value)) {
            throw $this->createValidationException(
                $context,
                'you must provide a class as value. Example @Slumber\AsObject( SomeClass::class )'
            );
        }

        // NOTICE: we also accept interfaces here, as the interface can define a Slumber\Polymorphic
        if (! class_exists($this->value) && ! interface_exists($this->value)) {
            throw $this->createValidationException(
                $context,
                "you provided the non-existing class '$this->value'"
            );
        }
    }

    /**
     * @return bool
     */
    public function keepNullValuesInCollections()
    {
        return false;
    }
}
