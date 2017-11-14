<?php
/**
 * File was created 30.09.2015 07:32
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb\Types;

use MongoDB\BSON\UTCDateTime;
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

        if ($value instanceof UTCDateTime) {
            return $value->toDateTime();
        }

        // compatibility
        /** @noinspection NotOptimalIfConditionsInspection */
        if (($value instanceof \ArrayAccess || is_array($value))
            && isset($value['date'], $value['tz'])
            && $value['date'] instanceof UTCDateTime
        ) {
            return (new \DateTime('now', new \DateTimeZone($value['tz'])))->setTimestamp($value['date']->toDatetime()->getTimestamp());
        }

        return null;
    }
}
