<?php
/**
 * File was created 30.09.2015 07:46
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property;

use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsObject;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Types\ValueHolder;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ObjectMapper extends AbstractPropertyMapper
{
    /** @var AsObject */
    private $options;

    /**
     * C'tor.
     *
     * @param AsObject $options
     */
    public function __construct(AsObject $options)
    {
        $this->options = $options;
    }

    /**
     * @return AsObject
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
        /**
         * unwrap any wrappers like LazyDbReference
         * @see LazyDbReference
         */
        if ($value instanceof ValueHolder) {
            $value = $value->getValue();
        }

        if ($value instanceof $this->options->value) {
            return $slumberer->slumber($value);
        }

        return null;
    }

    /**
     * @param Awaker $awaker
     * @param mixed  $value
     *
     * @return mixed
     */
    public function awake(Awaker $awaker, $value)
    {
        if ($value === null || (! is_array($value) && ! $value instanceof \ArrayAccess)) {
            return null;
        }

        $cls = new \ReflectionClass($this->options->value);

        return $awaker->awake($value, $cls);
    }
}
