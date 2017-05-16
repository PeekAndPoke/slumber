<?php
/**
 * File was created 11.03.2016 07:27
 */

namespace PeekAndPoke\Component\GeoJson;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class LineString
{
    /**
     * @var float[][]
     */
    private $coordinates;

    /**
     * LineString constructor.
     *
     * @param float[][] $coordinates
     */
    public function __construct($coordinates)
    {
        $this->coordinates = (array) $coordinates;
    }

    /**
     * @return \float[][]
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }
}
