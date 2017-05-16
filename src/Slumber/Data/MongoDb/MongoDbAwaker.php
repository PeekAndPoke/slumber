<?php
/**
 * File was created 03.03.2016 07:15
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use PeekAndPoke\Component\Slumber\Core\Codec\Awaker;
use PeekAndPoke\Component\Slumber\Data\Storage;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface MongoDbAwaker extends Awaker
{
    /**
     * @return Storage
     */
    public function getStorage();
}
