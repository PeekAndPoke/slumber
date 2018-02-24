<?php
/**
 * File was created 03.03.2016 13:36
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property\GeoJson;

use PeekAndPoke\Component\GeoJson\LineString;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\GeoJson\AsLineString;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\AbstractPropertyMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class LineStringMapper extends AbstractPropertyMapper
{
    /** @var AsLineString */
    private $options;

    public function __construct(AsLineString $options)
    {
        $this->options = $options;
    }

    /**
     * @return AsLineString
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param Slumberer  $slumberer
     * @param LineString $value
     *
     * @return mixed
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        if (! $value instanceof LineString) {
            return null;
        }

        return [
            'type'        => 'LineString',
            'coordinates' => $value->getCoordinates(),
        ];
    }

    /**
     * @param Awaker $awaker
     * @param mixed  $value
     *
     * @return LineString
     */
    public function awake(Awaker $awaker, $value)
    {
        /** @noinspection NotOptimalIfConditionsInspection */
        if (isset($value['type'], $value['coordinates']) && $value['type'] === 'LineString') {
            return LineString::fromLngLats($value['coordinates']);
        }

        return null;
    }
}
