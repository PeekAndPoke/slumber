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
 * AsKeyValuePairs transforms arrays into the form { 'k': ..., 'v': ... }
 *
 * Keys are preserved.
 *
 * The result of slumbering will look like:
 *
 * Input:
 *
 * <code>
 *   array ( 'a' => 'A', 'b' => 'B' )
 *   array ( 'C', 'B' )
 * </code>
 *
 * Output:
 *
 * <code>
 *   [ { 'k' : 'a', 'v' : 'A'}, { 'k' : 'b', 'v' : 'B'} ]
 *   [ { 'k' : '0', 'v' : 'C'}, { 'k' : '1', 'v' : 'D'} ]
 * </code>
 *
 * The keys ('k') will always be strings
 *
 * @Annotation
 * @Annotation\Target("PROPERTY")
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AsKeyValuePairs extends AsCollection
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
                'Example: @Slumber\AsKeyValuePairs( @Slumber\AsObject( SomeClass::class ) ) or ' .
                '@Slumber\AsKeyValuePairs( @Slumber\AsString() )'
            );
        }
    }
}
