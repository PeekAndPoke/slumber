<?php
/**
 * Created by gerk on 30.10.17 06:42
 */

namespace PeekAndPoke\Component\Slumber\Data\Error;

use Throwable;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class UnknownError extends StorageError
{
    final public function __construct($message = '', Throwable $previous = null)
    {
        parent::__construct($message, self::UNKNOWN, $previous);
    }
}
