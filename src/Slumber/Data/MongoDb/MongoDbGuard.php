<?php
/**
 * Created by gerk on 03.12.17 18:20
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use MongoDB\Driver\Exception\ConnectionException;
use MongoDB\Driver\Exception\WriteException;
use PeekAndPoke\Component\Slumber\Data\MongoDb\Error\MongoDbConnectionError;
use PeekAndPoke\Component\Slumber\Data\MongoDb\Error\MongoDbDuplicateError;
use PeekAndPoke\Component\Slumber\Data\MongoDb\Error\MongoDbUnknownError;

/**
 * Guards the execution of connecting, reading, writing and normalize exceptions
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MongoDbGuard
{

    public static function guard(callable $action)
    {
        try {
            return $action();
        }
            // duplicate key exception
        catch (WriteException $e) {
            throw MongoDbDuplicateError::from($e);
        }
            // connection exception
        catch (ConnectionException $e) {
            throw MongoDbConnectionError::from($e);
        }
            // general mongodb exception
        catch (\Exception $e) {
            throw MongoDbUnknownError::from($e);
        }
    }
}
