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
     * @return string a json string defining the index settings
     */
    public function def();

    /**
     * @return array json decoded "def"
     */
    public function getDefinition();
}
