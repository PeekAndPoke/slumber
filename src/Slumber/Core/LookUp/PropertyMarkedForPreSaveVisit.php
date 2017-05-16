<?php
/**
 * File was created 06.10.2015 06:34
 */

namespace PeekAndPoke\Component\Slumber\Core\LookUp;

use PeekAndPoke\Component\Slumber\Annotation\PropertyPreSaveVisitorMarker;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class PropertyMarkedForPreSaveVisit
{
    /** @var string The name of the property */
    public $propertyName;

    /** @var PropertyPreSaveVisitorMarker[] The marker annotations */
    public $markers = [];
}
