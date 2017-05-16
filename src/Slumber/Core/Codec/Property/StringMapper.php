<?php
/**
 * File was created 30.09.2015 07:58
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property;

use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsString;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class StringMapper extends AbstractPropertyMapper
{
    /** @var AsString */
    private $options;

    /**
     * C'tor.
     *
     * @param AsString $options
     */
    public function __construct(AsString $options)
    {
        $this->options = $options;
    }

    /**
     * @return AsString
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param Slumberer $slumberer
     * @param mixed     $value
     *
     * @return string
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        return is_scalar($value) ? (string) $value : null;
    }

    /**
     * @param Awaker $awaker
     * @param mixed  $value
     *
     * @return string
     */
    public function awake(Awaker $awaker, $value)
    {
        // also check for objects, since we could have things like \MongoId here
        return is_scalar($value) || is_object($value) ? (string) $value : null;
    }
}
