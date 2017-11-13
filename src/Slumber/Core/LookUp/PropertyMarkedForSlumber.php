<?php
/**
 * File was created 06.10.2015 06:34
 */

namespace PeekAndPoke\Component\Slumber\Core\LookUp;

use PeekAndPoke\Component\Psi\Functions\Unary\Matcher\IsInstanceOf;
use PeekAndPoke\Component\Psi\Psi;
use PeekAndPoke\Component\Slumber\Annotation\PropertyMappingMarker;
use PeekAndPoke\Component\Slumber\Annotation\PropertyMarker;
use PeekAndPoke\Component\Slumber\Core\Codec\Mapper;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class PropertyMarkedForSlumber
{
    /** @var string The name of the property */
    public $name;
    /** @var string The field name for the properties value in the storage. CAN BE equal to the propertyName */
    public $alias;
    /** @var PropertyMappingMarker The property marker */
    public $marker;
    /** @var PropertyMarker[] All additional markers on the property */
    public $allMarkers;
    /** @var Mapper */
    public $mapper;

    /**
     * The reflection property used for access
     *
     * CAUTION: this must NOT be populated before serializing. Classes of the reflection api cannot be serialized or
     *          unserialized
     *
     * @see EntityConfig::warmUp
     *
     * @var \ReflectionProperty
     */
    public $reflectionProperty;

    /**
     * PropertyMarkedForSlumber constructor.
     *
     * @param string                $propertyName
     * @param string                $alias
     * @param PropertyMappingMarker $marker
     * @param PropertyMarker[]      $allMarkers
     * @param Mapper                $mapper
     *
     * @return PropertyMarkedForSlumber
     */
    public static function create(
        $propertyName,
        $alias,
        PropertyMappingMarker $marker,
        $allMarkers,
        Mapper $mapper
    ) {
        $ret = new self;

        $ret->name       = $propertyName;
        $ret->alias      = $alias;
        $ret->marker     = $marker;
        $ret->allMarkers = Psi::it($allMarkers)->filter(new IsInstanceOf(PropertyMarker::class))->toArray();
        $ret->mapper     = $mapper;

        return $ret;
    }

    /**
     * @param string $type
     *
     * @return PropertyMarker|null
     */
    public function getFirstMarkerOf($type)
    {
        $is = new IsInstanceOf($type);

        foreach ($this->allMarkers as $additionalMarker) {
            if ($is($additionalMarker)) {
                return $additionalMarker;
            }
        }

        return null;
    }

    /**
     * @param string $type
     *
     * @return PropertyMarker[]
     */
    public function getMarkersOf($type)
    {
        return Psi::it($this->allMarkers)
            ->filter(new IsInstanceOf($type))
            ->toArray();
    }
}
