<?php
/**
 * Created by gerk on 30.10.17 06:23
 */

namespace PeekAndPoke\Component\Slumber\Data\Error;

use PeekAndPoke\Component\Slumber\Core\Exception\SlumberRuntimeException;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class StorageError extends SlumberRuntimeException
{
    const UNKNOWN = 99999;

    const DUPLICATE_KEY = 100;

    const CONNECTION = 200;
}
