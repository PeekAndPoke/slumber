<?php
/**
 * Created by gerk on 30.09.16 15:11
 */

namespace PeekAndPoke\Component\Slumber\Core\LookUp;

use PeekAndPoke\Component\Slumber\Annotation\PropertyMappingMarker;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class PropertyMarker2Mapper
{
    /** @var string */
    private $default;
    /** @var string[] */
    private $mappings;

    /**
     * Annotation2Mapper constructor.
     *
     * @param string   $default  Class name of the default mapper
     * @param string[] $mappings Mapping from annotation to mapper class
     */
    public function __construct($default, $mappings)
    {
        $this->default  = $default;    // TODO: get rid of the default... either we can or cannot map
        $this->mappings = $mappings;
    }

    /**
     * @param PropertyMappingMarker $marker
     *
     * @return mixed
     */
    public function createMapper(PropertyMappingMarker $marker)
    {
        $markerClass = \get_class($marker);
        $mapperClass = $this->mappings[$markerClass] ?? $this->default;

        // descend the children
        $child = $marker->getValue() instanceof PropertyMappingMarker
            ? $this->createMapper($marker->getValue())
            : null;

        return new $mapperClass($marker, $child);
    }
}
