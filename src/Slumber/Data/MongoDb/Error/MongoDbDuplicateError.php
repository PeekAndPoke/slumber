<?php
/**
 * Created by gerk on 30.10.17 06:59
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb\Error;

use MongoDB\Driver\Exception\WriteException;
use PeekAndPoke\Component\Slumber\Data\Error\DuplicateError;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MongoDbDuplicateError extends DuplicateError
{
    public static function from(WriteException $exception)
    {
        preg_match('/^E11000 duplicate key error collection: (.*) index: (.*) dup key: (.*)$/', $exception->getMessage(), $matches);

        $table = $matches[1] ?? '';
        $index = $matches[2] ?? '';
        $data  = $matches[3] ?? '';

        return new static($exception->getMessage(), $table, $index, $data, $exception);
    }
}
