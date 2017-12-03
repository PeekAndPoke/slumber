<?php
/**
 * Created by gerk on 30.10.17 06:59
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb\Error;

use PeekAndPoke\Component\Slumber\Data\Error\ConnectionError;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MongoDbConnectionError extends ConnectionError
{
    public static function from(\Exception $exception)
    {
        return new static($exception->getMessage(), $exception);
    }
}
