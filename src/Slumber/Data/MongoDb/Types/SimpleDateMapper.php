<?php
/**
 * File was created 30.09.2015 07:32
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb\Types;

use MongoDB\BSON\UTCDateTime;
use PeekAndPoke\Component\Psi\Psi\IsDateString;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsSimpleDate;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\AbstractPropertyMapper;
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
     * @param mixed     $value
     *
     * @return UTCDateTime
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        if ($value instanceof LocalDate) {
            $value = $value->getDate();
        }

        if (! $value instanceof \DateTimeInterface) {
            return null;
        }

        $millis = ($value->getTimestamp() * 1000) + ((int) ($value->format('u') / 1000));

        return new UTCDateTime($millis);
    }

    /**
     * @param Awaker $awaker
     * @param mixed  $value
     *
     * @return \DateTime|null
     */
    public function awake(Awaker $awaker, $value)
    {
        if ($value instanceof \DateTime) {
            return $value;
        }

        // compatibility in case a LocalDate was changed into a SimpleDate
        if ($this->isAwakeLocalDateCompatible($value)) {
            return (new LocalDate($value['date'], $value['tz']))->getDate();
        }

        // compatibility in case a string was change to a LocalDate
        if (IsDateString::isValidDateString($value)) {
            return new \DateTime($value);
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
