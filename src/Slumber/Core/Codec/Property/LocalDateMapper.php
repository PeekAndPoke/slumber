<?php
/**
 * File was created 30.09.2015 07:58
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property;

use PeekAndPoke\Component\Psi\Psi\IsDateString;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsLocalDate;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Types\LocalDate;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class LocalDateMapper extends AbstractPropertyMapper
{
    /** @var AsLocalDate */
    private $options;

    /**
     * C'tor.
     *
     * @param AsLocalDate $options
     */
    public function __construct(AsLocalDate $options)
    {
        $this->options = $options;
    }

    /**
     * @return AsLocalDate
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param Slumberer $slumberer
     * @param LocalDate $value
     *
     * @return array
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        if (!$value instanceof LocalDate) {
            return null;
        }

        return [
            'date' => $value->format('Y-m-d\TH:i:s.uP'),
            'tz' => $value->getTimezone()->getName(),
        ];
    }

    /**
     * @param Awaker $awaker
     * @param mixed  $value
     *
     * @return LocalDate
     */
    public function awake(Awaker $awaker, $value)
    {
        if ($value === null) {
            return null;
        }

        // check for a complex input with date and timezone
        if (isset($value['date'], $value['tz'])) {

            if (false === ($tz = @timezone_open($value['tz']))) {
                return null;
            }

            if (IsDateString::isValidDateString($value['date'])) {
                return new LocalDate($value['date'],  $tz);
            }

            return null;
        }

        // check for a simple input
        if (IsDateString::isValidDateString($value)) {
            return LocalDate::raw(new \DateTime($value));
        }

        return null;
    }
}
