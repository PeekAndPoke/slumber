<?php
/**
 * File was created 30.09.2015 07:58
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec\Property;

use PeekAndPoke\Component\Psi\Psi\IsDateString;
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
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d\TH:i:s.uP');
        }

        if ($value instanceof LocalDate) {
            return $value->format('Y-m-d\TH:i:s.uP');
        }

        return null;
    }

    /**
     * @param Awaker $awaker
     * @param mixed  $value
     *
     * @return \DateTime|null
     */
    public function awake(Awaker $awaker, $value)
    {
        if ($value === null) {
            return null;
        }

        if (IsDateString::isValidDateString($value)) {
            return new \DateTime($value);
        }

        if ($this->isAwakeLocalDateCompatible($value)) {
            return new \DateTime($value['date'], new \DateTimeZone($value['tz']));
        }

        return null;
    }

    /**
     * @param $value
     *
     * @return bool
     */
    private function isAwakeLocalDateCompatible($value)
    {
        return isset($value['date'], $value['tz'])
                    && IsDateString::isValidDateString($value['date'])
                    && $this->isTimezone($value['tz']);
    }

    /**
     * TODO: move this to Psi and make a IsTimezoneString
     * TODO: add more that are not in timezone_identifiers_list(): https://en.wikipedia.org/wiki/List_of_tz_database_time_zones
     *
     * @param $str
     *
     * @return bool
     */
    private function isTimezone($str)
    {
        static $identifiers;

        if ($identifiers === null) {
            $ids = timezone_identifiers_list();
            array_walk($ids, function (&$id) { $id = strtolower($id); });

            $identifiers = array_flip($ids);

            // add some more
            $identifiers['utc'] = true;
            $identifiers['etc/utc'] = true;
        }

        return isset($identifiers[strtolower($str)]);
    }
}
