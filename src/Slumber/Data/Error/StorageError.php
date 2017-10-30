<?php
/**
 * Created by gerk on 30.10.17 06:23
 */

namespace PeekAndPoke\Component\Slumber\Data\Error;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
abstract class StorageError extends \RuntimeException
{
    const UNKNOWN = 99999;

    const DUPLICATE_KEY = 100;
}
