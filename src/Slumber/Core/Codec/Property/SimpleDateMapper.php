<?php
/**
 * File was created 30.09.2015 07:58
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property;

use PeekAndPoke\Component\Psi\Functions\Unary\Matcher\IsDateString;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsSimpleDate;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Types\LocalDate;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class SimpleDateMapper extends AbstractPropertyMapper
{
    /** @var AsSimpleDate */
    private $options;

    /**
     * C'tor.
     *
     * @param AsSimpleDate $options
     */
    public function __construct(AsSimpleDate $options)
    {
        $this->options = $options;
    }

    /**
     * @return AsSimpleDate
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param Slumberer $slumberer
     * @param LocalDate $value
     *
     * @return string
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        if (!$value instanceof \DateTimeInterface) {
            return null;
        }

        return $value->format('c');
    }

    /**
     * @param Awaker $awaker
     * @param mixed  $value
     *
     * @return \DateTime|null
     */
    public function awake(Awaker $awaker, $value)
    {
        $isDateStr = new IsDateString();

        if ($value !== null && $isDateStr($value)) {
            return new \DateTime($value);
        }

        return null;
    }
}
