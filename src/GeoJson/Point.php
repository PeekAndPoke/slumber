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
    /** @var float */
    private $lat;
    /** @var float */
    private $lng;

    /**
     * Point constructor.
     *
     * @param float $lat
     * @param float $lng
     */
    public function __construct($lat, $lng)
    {
        $this->lat = (float) $lat;
        $this->lng = (float) $lng;
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
