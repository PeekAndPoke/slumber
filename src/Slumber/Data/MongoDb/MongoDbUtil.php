<?php
/**
 * File was created 11.04.2016 08:54
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use MongoDB\BSON\ObjectID;
use MongoDB\BSON\UTCDateTime;
use PeekAndPoke\Component\Psi\Psi\IsDateString;
use PeekAndPoke\Component\Toolbox\ArrayUtil;
use PeekAndPoke\Types\LocalDate;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MongoDbUtil
{
    /**
     * Ensures that the input will be transformed in to a keyless array.
     *
     * Why? Well when you have an array like
     *
     * [0 => 1, 2 => 2]
     *
     * it will converted to a json object in a query:
     *
     * {"0" : 1, "2" : 2}
     *
     * while you probably wanted to query with
     *
     * [1, 2]
     *
     * @param $input
     *
     * @return array
     */
    public static function ensureList($input)
    {
        return array_values(
            ArrayUtil::ensureArray($input)
        );
    }

    /**
     * @param mixed $id
     *
     * @return bool
     */
    public static function isValidMongoIdString($id)
    {
        return $id !== null && \strlen($id) === 24 && preg_match('/[a-fA-F0-9]{24}/', $id);
    }

    /**
     * @param mixed $subject
     *
     * @return ObjectID|mixed
     */
    public static function ensureMongoId($subject)
    {
        if ($subject instanceof ObjectID) {
            return $subject;
        }

        /** @noinspection ExceptionsAnnotatingAndHandlingInspection */
        return self::isValidMongoIdString($subject) ? new ObjectID($subject) : $subject;
    }

    /**
     * @param LocalDate|\DateTime|string $subject
     *
     * @return UTCDateTime|null
     */
    public static function ensureMongoDate($subject)
    {
        if (is_scalar($subject) && IsDateString::isValidDateString($subject)) {
            $subject = new \DateTime($subject);
        }

        if ($subject instanceof \DateTime) {
            return new UTCDateTime($subject->getTimestamp() * 1000);
        }

        if ($subject instanceof LocalDate) {
            return new UTCDateTime($subject->getTimestamp() * 1000);
        }

        return null;
    }
}
