<?php
/**
 * Created by gerk on 30.10.17 06:28
 */

namespace PeekAndPoke\Component\Slumber\Functional\MongoDb;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\CachedReader;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\Cache;
use MongoDB;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Data\EntityPool;
use PeekAndPoke\Component\Slumber\Data\EntityPoolImpl;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbCodecSet;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbEntityConfigReader;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbEntityConfigReaderCached;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbEntityConfigReaderImpl;
use PeekAndPoke\Component\Slumber\Data\MongoDb\MongoDbPropertyMarkerToMapper;
use PeekAndPoke\Component\Slumber\Data\Storage;
use PeekAndPoke\Component\Slumber\Helper\UnitTestServiceProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\NullLogger;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
abstract class SlumberMongoDbTestBase extends TestCase
{
    public const DB_NAME               = 'slumber_tests_db';
    public const MAIN_COLLECTION       = 'main_class';
    public const REFERENCED_COLLECTION = 'ref_class';

    /** @var ContainerInterface[] */
    static private $diPerClass = [];

    /**
     * @return ContainerInterface|UnitTestServiceProvider
     */
    protected static function getDi()
    {
        return self::$diPerClass[static::class] ?? self::$diPerClass[static::class] = new UnitTestServiceProvider();
    }

    /**
     * @return Cache
     */
    protected static function createCache()
    {
        $name = 'cache';

        if (! static::getDi()->has($name)) {
            static::getDi()->set($name, new ArrayCache());
        }

        return static::getDi()->get($name);
    }

    /**
     * @return AnnotationReader
     */
    protected static function createAnnotationReader()
    {
        $name = 'annotation-reader';

        if (! static::getDi()->has($name)) {

            // setup the annotation reader for autoload
//            AnnotationRegistry::registerLoader('class_exists');

            static::getDi()->set($name, new CachedReader(
                new AnnotationReader(),
                static::createCache(),
                true
            ));
        }

        return static::getDi()->get($name);
    }

    /**
     * @return MongoDbEntityConfigReader
     */
    protected static function createEntityConfigReader()
    {
        $name = 'entity-config-reader';

        if (! static::getDi()->has($name)) {

            static::getDi()->set(
                $name,
                new MongoDbEntityConfigReaderCached(
                    new MongoDbEntityConfigReaderImpl(
                        new AnnotatedEntityConfigReader(
                            static::getDi(),
                            static::createAnnotationReader(),
                            new MongoDbPropertyMarkerToMapper()
                        )
                    ),
                    static::createCache(),
                    'test',
                    true
                )
            );
        }

        return static::getDi()->get($name);
    }

    /**
     * @return EntityPool
     */
    protected static function createEntityPool()
    {
        return EntityPoolImpl::getInstance();
    }

    /**
     * @param Storage $storage
     *
     * @return MongoDbCodecSet
     */
    protected static function createCodecSet(Storage $storage)
    {
        $name = 'codec-set';

        if (! static::getDi()->has($name)) {
            static::getDi()->set(
                $name,
                new MongoDbCodecSet(
                    static::getDi(),
                    static::createEntityConfigReader(),
                    static::createEntityPool(),
                    $storage,
                    new NullLogger()
                )
            );
        }

        return static::getDi()->get($name);
    }

    /**
     * @return MongoDB\Client
     */
    protected static function createMongoClient()
    {
        $name = 'mongo-client';

        if (! static::getDi()->has($name)) {
            static::getDi()->set(
                $name,
                new MongoDB\Client('mongodb://localhost:27017', ['connect' => false])
            );
        }

        return static::getDi()->get($name);
    }

    /**
     * @return MongoDB\Database
     */
    protected static function createDatabase()
    {
        $name = 'mongo-database';

        if (! static::getDi()->has($name)) {
            static::getDi()->set(
                $name,
                static::createMongoClient()->selectDatabase(self::DB_NAME)
            );
        }

        return static::getDi()->get($name);
    }
}
