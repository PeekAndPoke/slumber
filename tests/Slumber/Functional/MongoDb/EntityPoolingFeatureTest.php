<?php
/**
 * Created by gerk on 13.11.17 17:04
 */

namespace PeekAndPoke\Component\Slumber\Functional\MongoDb;

use PeekAndPoke\Component\Slumber\Data\EntityPool;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestMainClass;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class EntityPoolingFeatureTest extends SlumberMongoDbTestBase
{

    public function testGettingObjectByIdTwiceGivesTheSameObject()
    {
        $subject = new UnitTestMainClass();
        $subject->setId('TEST001');

        self::$mainRepo->save($subject);

        $isInPool = self::$storage->getEntityPool()->has(new \ReflectionClass($subject), EntityPool::PRIMARY_ID, 'TEST001');

        $this->assertTrue($isInPool, 'After saving the entity must be in the pool');

        // load by id
        $reloaded = self::$mainRepo->findById('TEST001');

        $this->assertSame($subject, $reloaded, 'The reloaded entity must come from the pool and must be the same instance as the saved one');
    }


    public function testSavingANewObjectIsAddedToThePool()
    {
        // setup
        $subject = new UnitTestMainClass();
        $subject->setId('TEST001');

        self::$mainRepo->save($subject);

        // clear the pool
        self::$storage->getEntityPool()->clear();

        $this->assertCount(0, self::$storage->getEntityPool()->all(), 'The entity pool must be empty');

        // load by id
        /** @var UnitTestMainClass $reloaded */
        $reloaded = self::$mainRepo->findById('TEST001');

        $this->assertNotSame($subject, $reloaded, 'The entity must NOT come from the pool, since the pool must be empty');
        $this->assertSame($subject->getId(), $reloaded->getId(), 'Reloading must work');

        /** @var UnitTestMainClass $reloaded */
        $reloadedAgain = self::$mainRepo->findById('TEST001');

        $this->assertSame($reloaded, $reloadedAgain, 'Reloading a second time must return the entity from the pool');
    }
}
