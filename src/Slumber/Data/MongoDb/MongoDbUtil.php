<?php
/**
 * File was created 11.04.2016 08:54
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use MongoDB\BSON\ObjectID;
use PeekAndPoke\Component\Psi\Functions\Unary\Matcher\IsDateString;
use PeekAndPoke\Types\LocalDate;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MongoDbUtil
{
    /**
     * @param mixed $id
     *
     * @return bool
     */
    public static function isValidMongoIdString($id)
    {
        return $id !== null && strlen($id) === 24 && preg_match('/[a-fA-F0-9]{24}/', $id);
    }

    /**
     * @param mixed $subject
     *
     * @return \MongoId|mixed
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
     * @return \MongoDate|null
     */
    public static function ensureMongoDate($subject)
    {
        if (is_scalar($subject) && IsDateString::isValidDateString($subject)) {
            $subject = new \DateTime($subject);
        }

        if ($subject instanceof \DateTime) {
            return new \MongoDate($subject->getTimestamp());
        }

        if ($subject instanceof LocalDate) {
            return new \MongoDate($subject->getTimestamp());
        }

        return null;
    }

    /**
     * @param \MongoDate $date
     *
     * @return \DateTime
     */
    public static function toDateTime(\MongoDate $date)
    {
        return (new \DateTime())->setTimestamp($date->sec);
    }
}
