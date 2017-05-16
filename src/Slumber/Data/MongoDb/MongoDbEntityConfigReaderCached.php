<?php
/**
 * File was created 14.02.2016 00:18
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb;

use Doctrine\Common\Cache\Cache;
use PeekAndPoke\Component\Slumber\Core\LookUp\CachedEntityConfigLookUp;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MongoDbEntityConfigReaderCached extends CachedEntityConfigLookUp implements MongoDbEntityConfigReader
{
    /**
     * MongoDbCachingEntityConfigLookUp constructor.
     *
     * @param MongoDbEntityConfigReader $delegate
     * @param Cache                     $cache
     * @param string                    $prefix
     * @param bool                      $debug
     */
    public function __construct(MongoDbEntityConfigReader $delegate, Cache $cache, $prefix, $debug = false)
    {
        parent::__construct($delegate, $cache, $prefix, $debug);
    }
}
