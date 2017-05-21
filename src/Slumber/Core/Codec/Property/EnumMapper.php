<?php
/**
 * File was created 30.09.2015 07:58
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property;

use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsEnum;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Types\Enumerated;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class EnumMapper extends AbstractPropertyMapper
{
    /** @var AsEnum */
    private $options;

    /**
     * C'tor.
     *
     * @param AsEnum $options
     */
    public function __construct(AsEnum $options)
    {
        $this->options = $options;
    }

    /**
     * @return AsEnum
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param Slumberer  $slumberer
     * @param Enumerated $value
     *
     * @return string|null
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        if (!$value instanceof Enumerated) {
            return null;
        }

        return (string)$value->getValue();
    }

    /**
     * @param Awaker $awaker
     * @param mixed  $value
     *
     * @return Enumerated|null
     */
    public function awake(Awaker $awaker, $value)
    {
        /** @var Enumerated $enumClass */
        $enumClass = $this->options->getValue();
        return $enumClass::from($value);
    }
}
