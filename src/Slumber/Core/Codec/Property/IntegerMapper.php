<?php
/**
 * File was created 30.09.2015 07:58
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property;

use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsInteger;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class IntegerMapper extends AbstractPropertyMapper
{
    /** @var AsInteger */
    private $options;

    /**
     * C'tor.
     *
     * @param AsInteger $options
     */
    public function __construct(AsInteger $options)
    {
        $this->options = $options;
    }

    /**
     * @return AsInteger
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param Slumberer $slumberer
     * @param mixed     $value
     *
     * @return int
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        return is_scalar($value) ? (int) $value : 0;
    }

    /**
     * @param Awaker $awaker
     * @param mixed  $value
     *
     * @return int
     */
    public function awake(Awaker $awaker, $value)
    {
        return (int) $value;
    }
}
