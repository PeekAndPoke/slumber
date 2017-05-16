<?php
/**
 * File was created 30.09.2015 09:44
 */

namespace PeekAndPoke\Component\Slumber\Annotation;

use PeekAndPoke\Component\Slumber\Core\Exception\SlumberException;
use PeekAndPoke\Component\Slumber\Core\Validation\ClassAnnotationValidationContext;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface ClassMarker extends SlumberMarker
{
    /**
     * @param ClassAnnotationValidationContext $context
     *
     * @throws SlumberException
     */
    public function validate($context);
}
