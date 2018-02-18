<?php
/**
 * File was created 30.09.2015 09:44
 */

namespace PeekAndPoke\Component\Slumber\Annotation;

use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;
use PeekAndPoke\Component\Slumber\Core\Validation\ValidationContext;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface PropertyMappingMarker extends PropertyMarker
{
    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return string
     */
    public function getAlias();

    /**
     * @return bool
     */
    public function hasAlias();

    /**
     * @return bool
     */
    public function keepNullValuesInCollections();

    /**
     * @param ValidationContext $context
     *
     * @throws SlumberException
     */
    public function validate(ValidationContext $context);
}
