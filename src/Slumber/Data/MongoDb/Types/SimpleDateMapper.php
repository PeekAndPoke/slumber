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
        if (! $value instanceof \DateTimeInterface) {
            return null;
        }

        return new UTCDateTime($value->getTimestamp() * 1000);
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
        $canAccess     = $value instanceof \ArrayAccess || is_array($value);
        $hasComponents = isset($value['date'], $value['tz']);

        if ($canAccess && $hasComponents && $value['date'] instanceof \DateTime) {
            return (new \DateTime('now', new \DateTimeZone($value['tz'])))->setTimestamp($value['date']->getTimestamp());
        }

        // compatibility in case a string was change to a LocalDate
        if (IsDateString::isValidDateString($value)) {
            return new \DateTime($value);
        }

        return null;
    }
}
