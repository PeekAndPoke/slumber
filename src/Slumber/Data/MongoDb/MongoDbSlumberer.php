<?php
/**
 * File was created 03.03.2016 07:16
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;
use PeekAndPoke\Component\Slumber\Data\Storage;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface MongoDbSlumberer extends Slumberer
{
    /**
     * @return Storage
     */
    public function getStorage();
}
