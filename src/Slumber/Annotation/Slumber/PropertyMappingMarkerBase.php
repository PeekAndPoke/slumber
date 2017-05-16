<?php
/**
 * File was created 30.09.2015 17:51
 */

namespace PeekAndPoke\Component\Slumber\Annotation\Slumber;

use PeekAndPoke\Component\Slumber\Annotation\PropertyMappingMarker;
use PeekAndPoke\Component\Slumber\Annotation\SlumberAnnotation;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
abstract class PropertyMappingMarkerBase extends SlumberAnnotation implements PropertyMappingMarker
{
    /**
     * Set an alias for this field. The alias will be used for storing the value in the database.
     *
     * @var string
     */
    public $alias;

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return $this->alias;
    }

    /**
     * @return bool
     */
    public function hasAlias()
    {
        return ! empty($this->alias);
    }

    /**
     * @return bool
     */
    public function keepNullValuesInCollections()
    {
        return true;
    }
}
