<?php
/**
 * File was created 30.09.2015 07:42
 */

namespace PeekAndPoke\Component\Slumber\Annotation;

use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;
use PeekAndPoke\Component\Slumber\Core\Validation\ValidationContext;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface SlumberMarker
{
    /**
     * Initialize the annotation and validate the given parameters
     *
     * @param ValidationContext $context
     *
     * @throws SlumberException
     */
    public function validate($context); // TODO: add type hint to method signature
}
