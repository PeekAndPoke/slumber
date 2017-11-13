<?php
/**
 * Created by gerk on 13.11.17 06:50
 */

use PeekAndPoke\Component\PropertyAccess\PublicPropertyAccess;
use PeekAndPoke\Component\PropertyAccess\ReflectionPropertyAccess;
use PeekAndPoke\Component\PropertyAccess\ScopedPropertyAccess;

require_once __DIR__ . '/../vendor/autoload.php';


/** @noinspection PhpUndefinedClassInspection */
/** @noinspection AutoloadingIssuesInspection */
class A
{

    /** @var int */
    public $publicProp;

    /** @var int */
    private $privateProp;

    /**
     * @return int
     */
    public function getPublicProp()
    {
        return $this->publicProp;
    }

    /**
     * @return int
     */
    public function getPrivateProp()
    {
        return $this->privateProp;
    }
}


$iterations = 1000000;

function benchmarkPublicPropAccessGet($iterations)
{
    $object   = new A();
    $accessor = PublicPropertyAccess::create('publicProp');

    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {

        $accessor->get($object);
    }

    $end      = microtime(true);
    $duration = $end - $start;

    echo "$iterations x PublicPropertyAccess::get took $duration sec \n";
}

function benchmarkPublicPropAccessSet($iterations)
{
    $object   = new A();
    $accessor = PublicPropertyAccess::create('publicProp');

    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {

        $accessor->set($object, $i);
    }

    $end      = microtime(true);
    $duration = $end - $start;

    echo "$iterations x PublicPropertyAccess::set took $duration sec \n";
}

function benchmarkScopedPropAccessGet($iterations)
{
    $object   = new A();
    $accessor = ScopedPropertyAccess::create(A::class, 'privateProp');

    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {

        $accessor->get($object);
    }

    $end      = microtime(true);
    $duration = $end - $start;

    echo "$iterations x ScopedPropertyAccess::get took $duration sec \n";
}

function benchmarkScopedPropAccessSet($iterations)
{
    $object   = new A();
    $accessor = ScopedPropertyAccess::create(A::class, 'privateProp');

    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {

        $accessor->set($object, $i);
    }

    $end      = microtime(true);
    $duration = $end - $start;

    echo "$iterations x ScopedPropertyAccess::set took $duration sec \n";
}

function benchmarkReflectionPropAccessGet($iterations)
{
    $object   = new A();
    $accessor = ReflectionPropertyAccess::create(new \ReflectionClass(A::class), 'privateProp');

    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {

        $accessor->get($object);
    }

    $end      = microtime(true);
    $duration = $end - $start;

    echo "$iterations x ReflectionPropertyAccess::get took $duration sec \n";
}

function benchmarkReflectionPropAccessSet($iterations)
{
    $object   = new A();
    $accessor = ReflectionPropertyAccess::create(new \ReflectionClass(A::class), 'privateProp');

    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {

        $accessor->set($object, $i);
    }

    $end      = microtime(true);
    $duration = $end - $start;

    echo "$iterations x ReflectionPropertyAccess::set took $duration sec \n";
}


benchmarkPublicPropAccessGet($iterations);
benchmarkPublicPropAccessSet($iterations);

benchmarkScopedPropAccessGet($iterations);
benchmarkScopedPropAccessSet($iterations);

benchmarkReflectionPropAccessGet($iterations);
benchmarkReflectionPropAccessSet($iterations);
