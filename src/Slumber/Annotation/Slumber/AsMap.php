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
 * AsMap treats the nested elements as a key value map.
 *
 * Keys are preserved.
 *
 * The result of slumbering will look like:
 *
 * Input:
 * <code>
 *   array ( 'a' => 'A', 'b' => 'B' )
 *   array ( 'C', 'B' )
 * </code>
 *
 * Output:
 * <code>
 *   { 'a' : 'A', 'b' : 'B' }
 *   { '0' : 'C', '1' : 'D' )
 * </code>
 *
 * @Annotation
 * @Annotation\Target("PROPERTY")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AsMap extends AsCollection
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
                'Example: @Slumber\AsMap( @Slumber\AsObject( SomeClass::class ) ) or ' .
                '@Slumber\AsMap( @Slumber\AsString() )'
            );
        }
    }
}
