<?php
/**
 * File was created 30.09.2015 07:46
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property;

use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsDecimal;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class DecimalMapper extends AbstractPropertyMapper
{
    /** @var AsDecimal */
    private $options;

    /**
     * C'tor
     *
     * @param AsDecimal $options
     */
    public function __construct(AsDecimal $options)
    {
        $this->options = $options;
    }

    /**
     * @return AsDecimal
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param Slumberer $slumberer
     * @param mixed     $value
     *
     * @return float
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        return is_scalar($value) ? (float) $value : 0.0;
    }

    /**
     * @param Awaker $awaker
     * @param mixed  $value
     *
     * @return float
     */
    public function awake(Awaker $awaker, $value)
    {
        return is_scalar($value) ? (float) $value : 0.0;
    }
}
