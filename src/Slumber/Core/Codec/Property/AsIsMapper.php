<?php
/**
 * File was created 30.09.2015 07:46
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property;

use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsIs;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class AsIsMapper extends AbstractPropertyMapper
{
    /** @var AsIs */
    private $options;

    /**
     * C'tor.
     *
     * @param AsIs $options
     */
    public function __construct(AsIs $options)
    {
        $this->options = $options;
    }

    /**
     * @return AsIs
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param Slumberer $slumberer
     * @param mixed     $value
     *
     * @return mixed
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        return $value;
    }

    /**
     * @param Awaker $awaker
     * @param mixed  $value
     *
     * @return mixed
     */
    public function awake(Awaker $awaker, $value)
    {
        return $value;
    }
}
