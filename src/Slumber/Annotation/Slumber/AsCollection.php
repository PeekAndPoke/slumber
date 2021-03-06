<?php
/**
 * File was created 30.09.2015 10:49
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber;

use PeekAndPoke\Component\Collections\Collection;
use PeekAndPoke\Component\Slumber\Annotation\PropertyMappingMarker;
use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;
use PeekAndPoke\Component\Slumber\Core\Validation\ValidationContext;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
abstract class AsCollection extends PropertyMappingMarkerBase
{
    /**
     * @var string The class of the collection to use
     */
    public $collection;

    /**
     * @return string
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * @param string $collection
     *
     * @return $this
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * @param ValidationContext $context
     *
     * @throws SlumberException
     */
    public function validate(ValidationContext $context)
    {
        $this->validateNestedMapperParam($context);

        $this->validateCollectionParam($context);
    }

    public function validateNestedMapperParam(ValidationContext $context)
    {
        if ($this->value instanceof PropertyMappingMarker) {

            $this->value->validate($context);

        } else {
            throw $this->createValidationException(
                $context,
                'You must provide a PropertyMappingMarker as value. ' .
                'Example: @Slumber\AsList( @Slumber\AsObject( SomeClass::class ) ) or ' .
                '@Slumber\AsList( @Slumber\AsString() )'
            );
        }
    }

    public function validateCollectionParam(ValidationContext $context)
    {
        if (! empty ($this->collection)) {

            if (! class_exists($this->collection)) {
                throw $this->createValidationException(
                    $context,
                    "The collection class '{$this->collection}' does not exist'"
                );
            }

            $expectedType = Collection::class;

            if (! is_a($this->collection, $expectedType, true)) {
                throw $this->createValidationException(
                    $context,
                    "The collection class '{$this->collection}' must be instance of '{$expectedType}'"
                );
            }
        }
    }
}
