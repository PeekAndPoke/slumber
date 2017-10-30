<?php
/**
 * Created by gerk on 30.10.17 06:42
 */

namespace PeekAndPoke\Component\Slumber\Data\Error;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class UnknownError extends StorageError
{
    final public function __construct($message = '', \Exception $previous = null)
    {
        parent::__construct($message, self::UNKNOWN, $previous);
    }
}
