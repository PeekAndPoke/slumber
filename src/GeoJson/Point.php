<?php
/**
 * File was created 03.03.2016 13:39
 */

namespace PeekAndPoke\Component\GeoJson;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class Point
{
    public const TYPE = 'Point';

    /** @var float */
    private $lat;
    /** @var float */
    private $lng;

    /**
     * @param float $lng
     * @param float $lat
     *
     * @return Point
     */
    public static function fromLngLat($lng, $lat)
    {
        $ret = new self;

        $ret->lng = $lng;
        $ret->lat = $lat;

        return $ret;
    }

    /**
     */
    protected function __construct()
    {
    }

    /**
     * @return float
     */
    public function getLat()
    {
        return $this->lat;
    }

    /**
     * @return float
     */
    public function getLng()
    {
        return $this->lng;
    }
}
