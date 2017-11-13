<?php
/**
 * Created by gerk on 09.11.17 16:32
 */

namespace PeekAndPoke\Component\Slumber\Mocks;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Cache\ArrayCache;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodec;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Core\LookUp\CachedEntityConfigLookUp;

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

        return $i ?: $i = new UnitTestServiceProvider();
    }

    public function getArrayCodec()
    {
        static $i;

        if ($i) {
            return $i;
        }

        // setup the annotation reader for autoload
        AnnotationRegistry::registerLoader(
            function ($class) {
                return class_exists($class) || interface_exists($class) || trait_exists($class);
            }
        );

        $i = new ArrayCodec(
            new CachedEntityConfigLookUp(
                new AnnotatedEntityConfigReader(
                    $this->getDi(),
                    new AnnotationReader(),
                    new ArrayCodecPropertyMarker2Mapper()
                ),
                new ArrayCache(),
                '[Slumber-Test]@',
                true
            )
        );

        return $i;
    }

}
