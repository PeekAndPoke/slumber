<?php
/**
 * File was created 01.03.2016 12:14
 */

namespace PeekAndPoke\Component\Slumber\Annotation;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface CompoundIndexDefinition extends ClassMarker, IndexDefinition
{
    /**
     * @return array the index definition
     */
    public function getDefinition();
}
