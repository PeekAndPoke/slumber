<?php
/**
 * File was created 30.09.2015 07:58
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property;

use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsBool;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class BoolMapper extends AbstractPropertyMapper
{
    /** @var AsBool */
    private $options;

    /**
     * C'tor.
     *
     * @param AsBool $options
     */
    public function __construct(AsBool $options)
    {
        $this->options = $options;
    }

    /**
     * @return AsBool
     */
    public function getOptions() : AsBool
    {
        return $this->options;
    }

    /**
     * @param Slumberer $slumberer
     * @param mixed     $value
     *
     * @return bool
     */
    public function slumber(Slumberer $slumberer, $value) : bool
    {
        return (bool) $value;
    }

    /**
     * @param Awaker $awaker
     * @param mixed  $value
     *
     * @return bool
     */
    public function awake(Awaker $awaker, $value) : bool
    {
        return (bool) $value;
    }
}
