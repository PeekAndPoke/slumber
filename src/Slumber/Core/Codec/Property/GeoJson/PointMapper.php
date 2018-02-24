<?php
/**
 * File was created 03.03.2016 13:36
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property\GeoJson;

use PeekAndPoke\Component\GeoJson\Point;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\GeoJson\AsPoint;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\AbstractPropertyMapper;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class PointMapper extends AbstractPropertyMapper
{
    /** @var AsPoint */
    private $options;

    /**
     * @param AsPoint $options
     */
    public function __construct(AsPoint $options)
    {
        $this->options = $options;
    }

    /**
     * @return AsPoint
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param Slumberer $slumberer
     * @param Point     $value
     *
     * @return mixed
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        if (! $value instanceof Point) {
            return null;
        }

        return [
            'type'        => Point::TYPE,
            'coordinates' => [
                $value->getLng(),
                $value->getLat(),
            ],
        ];
    }

    /**
     * @param Awaker $awaker
     * @param mixed  $value
     *
     * @return Point
     */
    public function awake(Awaker $awaker, $value)
    {
        /** @noinspection NotOptimalIfConditionsInspection */
        if (isset($value['type'], $value['coordinates']) && $value['type'] === 'Point') {

            $coords = $value['coordinates'];

            if (\count($coords) === 2) {
                return Point::fromLngLat($coords[0], $coords[1]);
            }
        }

        return null;
    }
}
