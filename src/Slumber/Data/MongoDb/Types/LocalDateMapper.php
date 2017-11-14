<?php
/**
 * File was created 30.09.2015 07:32
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb\Types;

use MongoDB\BSON\UTCDateTime;
use PeekAndPoke\Component\Psi\Functions\Unary\Matcher\IsDateString;
use PeekAndPoke\Component\Slumber\Annotation\Slumber\AsLocalDate;
use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Core\Codec\Property\AbstractPropertyMapper;
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
     * @param mixed     $value
     *
     * @return array
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        if (! $value instanceof LocalDate) {
            return null;
        }

        return [
            'date' => new UTCDateTime($value->getTimestamp() * 1000),
            'tz'   => $value->getTimezone()->getName(),
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
        // default cases first
        $canAccess     = $value instanceof \ArrayAccess || is_array($value);
        $hasComponents = isset($value['date'], $value['tz']);

        if ($canAccess && $hasComponents && $value['date'] instanceof \DateTime) {
            return new LocalDate($value['date'], $value['tz']);
        }

        // compatibility in case a SimpleDate was changed to a LocalDate
        if ($value instanceof \DateTime) {
            return LocalDate::raw($value);
        }

        // compatibility in case a string was change to a LocalDate
        if (IsDateString::isValidDateString($value)) {
            return LocalDate::raw(new \DateTime($value));
        }

        return null;
    }
}
