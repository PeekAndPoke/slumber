<?php
/**
 * Created by gerk on 19.09.16 09:46
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Slumber\Annotation\SlumberAnnotation;
use PeekAndPoke\Component\Slumber\Core\Validation\ValidationContext;


/**
 * Use this annotation to give a class an alias.
 *
 * The can be useful for polymorphics.
 *
 * It will also be picked up by libraries like Rip.
 *
 * @Annotation
 * @Annotation\Target("CLASS")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class Alias extends SlumberAnnotation
{
    /**
     * @return string
     */
    public function getAlias()
    {
        return (string) $this->value;
    }

    public function validate(ValidationContext $context)
    {
        if (empty($this->value)) {
            throw $this->createValidationException(
                $context,
                'The type alias must not have an empty value.'
            );
        }

        if (! \is_string($this->value)) {
            throw $this->createValidationException(
                $context,
                'The type alias must be a string'
            );
        }
    }
}
