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
            'type'        => 'Point',
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
        if (($value instanceof \ArrayAccess || \is_array($value))
            && $value['type'] === 'Point'
            && isset($value['type'], $value['coordinates'])
            && \count($value['coordinates']) === 2
        ) {
            $coords = $value['coordinates'];

            return new Point($coords[1], $coords[0]);
        }

        return null;
    }
}
