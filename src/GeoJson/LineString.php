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
    public const TYPE = 'LineString';

    /**
     * @var float[][]
     */
    private $coordinates;

    /**
     * Create a LineString object from coordinates
     *
     * The coordinates must be in Long-Lat order. Example:
     *
     * <code>
     *
     * LineString::from([10.1, 20.2, 10.2, 20.3])
     *
     * </code>
     *
     * @param array $coordinates The coordinates as pairs of Long-Lat
     *
     * @return LineString
     */
    public static function fromLngLats(array $coordinates)
    {
        $ret = new self;

        $ret->coordinates = $coordinates;

        return $ret;
    }

    /**
     */
    protected function __construct()
    {
    }

    /**
     * @return \float[][]
     */
    public function getCoordinates()
    {
        return $this->coordinates;
    }
}
