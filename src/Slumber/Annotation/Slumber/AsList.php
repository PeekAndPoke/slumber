<?php
/**
 * File was created 30.09.2015 10:49
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber;

use Doctrine\Common\Annotations\Annotation;
use PeekAndPoke\Component\Slumber\Annotation\PropertyMappingMarker;
use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;
use PeekAndPoke\Component\Slumber\Core\Validation\PropertyAnnotationValidationContext;

/**
 * AsList treats the nested elements as a list.
 *
 * Keys are NOT preserved and are removed.
 *
 * The result of slumbering will look like:
 *
 * Input:
 * <code>
 *   array ( 'a' => 'A', 'b' => 'B' )
 * </code>
 *
 * Output:
 * <code>
 *   [ 'A', 'B' ]
 * </code>
 *
 * @Annotation
 * @Annotation\Target("PROPERTY")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AsList extends AsCollection
{
    /**
     * @param PropertyAnnotationValidationContext $context
     *
     * @throws SlumberException
     */
    public function validate($context)
    {
        if ($this->value instanceof PropertyMappingMarker) {

            $this->value->validate($context);

        } else {
            throw $this->createValidationException(
                $context,
                'you must provide an ISlumberPropertyMarker as value. ' .
                'Example: @Slumber\AsList( @Slumber\AsObject( SomeClass::class ) ) or ' .
                '@Slumber\AsList( @Slumber\AsString() )'
            );
        }
    }
}
