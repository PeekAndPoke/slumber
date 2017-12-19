<?php
/**
 * Created by gerk on 18.11.17 20:43
 */

namespace PeekAndPoke\Component\Slumber\Unit\Data;

use PeekAndPoke\Component\Slumber\Data\EntityPoolImpl;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class EntityPoolImplTest extends TestCase
{
    public function testAllWhenPoolIsEmpty()
    {
        $subject = EntityPoolImpl::getInstance();

        $this->assertSame([], $subject->all(), 'all() must work correct when pool is empty');
    }

    public function testStatsWhenPoolIsEmpty()
    {
        $subject = EntityPoolImpl::getInstance();

        $stats = $subject->stats();

        $this->assertSame(0, $stats->getNumEntries(), 'stats.numEntries must be null when pool is empty');
        $this->assertSame(0, $stats->getNumHits(), 'stats.numHits must be null when pool is empty');
        $this->assertSame(0, $stats->getNumMisses(), 'stats.numMisses must be null when pool is empty');
    }

    public function testSetGetHas()
    {
        $subject = EntityPoolImpl::getInstance();

        $class1 = new \ReflectionClass(\stdClass::class);
        $class2 = new \ReflectionClass(\DateTime::class);

        ////  BEFORE populating the pool  ////////////////////////////////////////////////////////////////////////////////////////////////////////
        $this->assertFalse($subject->has($class1, 'idKey', 'id1'), 'before setting nothing must be found in the pool');
        $this->assertFalse($subject->has($class1, 'idKey', 'id2'), 'before setting nothing must be found in the pool');
        $this->assertFalse($subject->has($class1, 'idKey', 'id3'), 'before setting nothing must be found in the pool');

        $this->assertFalse($subject->has($class2, 'idKey', 'id1'), 'before setting nothing must be found in the pool');
        $this->assertFalse($subject->has($class2, 'idKey', 'id2'), 'before setting nothing must be found in the pool');
        $this->assertFalse($subject->has($class2, 'idKey', 'id3'), 'before setting nothing must be found in the pool');

        ////  AFTER populating the pool  /////////////////////////////////////////////////////////////////////////////////////////////////////////
        $subject->set($class1, 'idKey', 'id1', 'value1_1');
        $subject->set($class1, 'idKey', 'id2', 'value1_2');
        $subject->set($class1, 'idKey', 'id3', 'value1_3');

        $subject->set($class2, 'idKey', 'id1', 'value2_1');
        $subject->set($class2, 'idKey', 'id2', 'value2_2');
        $subject->set($class2, 'idKey', 'id3', 'value2_3');

        $this->assertTrue($subject->has($class1, 'idKey', 'id1'), 'After setting entries must be found in the pool');
        $this->assertTrue($subject->has($class1, 'idKey', 'id2'), 'After setting entries must be found in the pool');
        $this->assertTrue($subject->has($class1, 'idKey', 'id3'), 'After setting entries must be found in the pool');

        $this->assertSame('value1_1', $subject->get($class1, 'idKey', 'id1'), 'Value must be retrieved correctly from the pool');
        $this->assertSame('value1_2', $subject->get($class1, 'idKey', 'id2'), 'Value must be retrieved correctly from the pool');
        $this->assertSame('value1_3', $subject->get($class1, 'idKey', 'id3'), 'Value must be retrieved correctly from the pool');

        $this->assertTrue($subject->has($class2, 'idKey', 'id1'), 'After setting entries must be found in the pool');
        $this->assertTrue($subject->has($class2, 'idKey', 'id2'), 'After setting entries must be found in the pool');
        $this->assertTrue($subject->has($class2, 'idKey', 'id3'), 'After setting entries must be found in the pool');

        $this->assertSame('value2_1', $subject->get($class2, 'idKey', 'id1'), 'Value must be retrieved correctly from the pool');
        $this->assertSame('value2_2', $subject->get($class2, 'idKey', 'id2'), 'Value must be retrieved correctly from the pool');
        $this->assertSame('value2_3', $subject->get($class2, 'idKey', 'id3'), 'Value must be retrieved correctly from the pool');

        ////  THINGS not in the pool  ////////////////////////////////////////////////////////////////////////////////////////////////////////
        $class3 = new \ReflectionClass(\DateTimeZone::class);

        $this->assertFalse($subject->has($class3, 'idKey', 'id1'), 'Nothing must be found for another class in the pool');
        $this->assertFalse($subject->has($class3, 'idKey', 'id2'), 'Nothing must be found for another class in the pool');
        $this->assertFalse($subject->has($class3, 'idKey', 'id3'), 'Nothing must be found for another class in the pool');

        ////  AFTER cleaning not in the pool  ///////////////////////////////////////////////////////////////////////////////////////////////////
        $subject->clear();

        $this->assertFalse($subject->has($class1, 'idKey', 'id1'), 'before setting nothing must be found in the pool');
        $this->assertFalse($subject->has($class1, 'idKey', 'id2'), 'before setting nothing must be found in the pool');
        $this->assertFalse($subject->has($class1, 'idKey', 'id3'), 'before setting nothing must be found in the pool');

        $this->assertFalse($subject->has($class2, 'idKey', 'id1'), 'before setting nothing must be found in the pool');
        $this->assertFalse($subject->has($class2, 'idKey', 'id2'), 'before setting nothing must be found in the pool');
        $this->assertFalse($subject->has($class2, 'idKey', 'id3'), 'before setting nothing must be found in the pool');
    }
}
