<?php
/**
 * File was created 30.09.2015 07:32
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec;

use PeekAndPoke\Component\Slumber\Annotation\PropertyMappingMarker;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface Mapper
{
    /**
     * @return PropertyMappingMarker
     */
    public function getOptions();

    /**
     * @param Slumberer $slumberer
     * @param mixed     $value
     *
     * @return mixed
     */
    public function slumber(Slumberer $slumberer, $value);

    /**
     * @param Awaker $awaker
     * @param mixed  $value
     *
     * @return mixed
     */
    public function awake(Awaker $awaker, $value);
}
