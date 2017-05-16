<?php
/**
 * File was created 29.02.2016 16:55
 */

namespace PeekAndPoke\Component\Slumber\Core\LookUp;

use PeekAndPoke\Component\Slumber\Annotation\PropertyStorageIndexMarker;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class PropertyMarkedForIndexing
{
    /**
     * @var string                        The name of the property
     */
    public $propertyName;
    /**
     * @var PropertyStorageIndexMarker[] The marker annotations
     */
    public $markers = [];
}
