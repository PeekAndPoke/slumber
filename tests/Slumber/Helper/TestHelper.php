<?php
/**
 * Created by gerk on 09.11.17 16:32
 */

namespace PeekAndPoke\Component\Slumber\Helper;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodec;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Core\LookUp\CachedEntityConfigLookUp;
use PeekAndPoke\Component\Slumber\StaticServiceProvider;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class TestHelper
{
    public static function getInstance()
    {
        static $inst;

        return $inst ?: $inst = new static;
    }

    protected function __construct() { }

    public function getDi()
    {
        static $i;

        return $i ?: $i = new StaticServiceProvider();
    }

    public function getArrayCodec()
    {
        static $i;

        if ($i) {
            return $i;
        }

        $i = new ArrayCodec(
            new CachedEntityConfigLookUp(
                new AnnotatedEntityConfigReader($this->getDi(), new AnnotationReader(), new ArrayCodecPropertyMarker2Mapper()),
                new ArrayCache(),
                '[Slumber-Test]@',
                true
            )
        );

        return $i;
    }
}
