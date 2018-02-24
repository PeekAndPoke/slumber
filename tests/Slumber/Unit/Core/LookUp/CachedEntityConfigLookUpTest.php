<?php
/**
 * Created by gerk on 24.02.18 09:30
 */

namespace PeekAndPoke\Component\Slumber\Unit\Core\LookUp;

use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Cache\ArrayCache;
use PeekAndPoke\Component\Slumber\Annotation\Slumber;
use PeekAndPoke\Component\Slumber\Core\Codec\ArrayCodecPropertyMarker2Mapper;
use PeekAndPoke\Component\Slumber\Core\LookUp\AnnotatedEntityConfigReader;
use PeekAndPoke\Component\Slumber\Core\LookUp\CachedEntityConfigLookUp;
use PeekAndPoke\Component\Slumber\StaticServiceProvider;
use PHPUnit\Framework\TestCase;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class CachedEntityConfigLookUpTest extends TestCase
{
    public function testInDevMode()
    {
        $cache   = new ArrayCache();
        $subject = $this->createSubjectInDevMode($cache);

        $first  = $subject->getEntityConfig(new \ReflectionClass(TestClassForCachedEntityConfigLookUpTest::class));
        $second = $subject->getEntityConfig(new \ReflectionClass(TestClassForCachedEntityConfigLookUpTest::class));

        $this->assertSame($first, $second);
    }

    public function testInDevModeWithReconstruction()
    {
        $cache = new ArrayCache();

        $subject = $this->createSubjectInDevMode($cache);
        $first   = $subject->getEntityConfig(new \ReflectionClass(TestClassForCachedEntityConfigLookUpTest::class));

        $subject = $this->createSubjectInDevMode($cache);
        $second  = $subject->getEntityConfig(new \ReflectionClass(TestClassForCachedEntityConfigLookUpTest::class));

        $this->assertSame($first, $second);
    }

    public function testBuiltInClass()
    {
        $cache = new ArrayCache();

        $subject = $this->createSubjectInDevMode($cache);
        $first   = $subject->getEntityConfig(new \ReflectionClass(\DateTime::class));

        $subject = $this->createSubjectInDevMode($cache);
        $second  = $subject->getEntityConfig(new \ReflectionClass(\DateTime::class));

        $this->assertSame($first, $second);
    }

    private function createSubjectInDevMode(ArrayCache $cache) : CachedEntityConfigLookUp
    {
        $delegate = new AnnotatedEntityConfigReader(
            new StaticServiceProvider(),
            new AnnotationReader(),
            new ArrayCodecPropertyMarker2Mapper()
        );

        return new CachedEntityConfigLookUp($delegate, $cache, '[TESTS]@', true);
    }
}

/**
 * @internal For unit tests only
 */
class TestClassForCachedEntityConfigLookUpTest
{
    /**
     * @var float
     *
     * @Slumber\AsDecimal()
     */
    protected $protectedMarkedAsDecimalOnBase;

    /**
     * @var string|null
     *
     * @Slumber\AsString()
     */
    private $privateMarkedAsStringOnBase;

    /**
     * @return null|string
     */
    public function getPrivateMarkedAsStringOnBase()
    {
        return $this->privateMarkedAsStringOnBase;
    }

    /**
     * @param null|string $privateMarkedAsStringOnBase
     *
     * @return $this
     */
    public function setPrivateMarkedAsStringOnBase($privateMarkedAsStringOnBase)
    {
        $this->privateMarkedAsStringOnBase = $privateMarkedAsStringOnBase;

        return $this;
    }
}
