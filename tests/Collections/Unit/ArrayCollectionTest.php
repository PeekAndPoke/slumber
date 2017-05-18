<?php
/**
 * Created by gerk on 18.05.17 16:41
 */

namespace PeekAndPoke\Component\Collections\Unit;

use PeekAndPoke\Component\Collections\ArrayCollection;
use PeekAndPoke\Component\Psi\Psi;
use PHPUnit\Framework\TestCase;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ArrayCollectionTest extends TestCase
{
    /**
     * @param $data
     *
     * @dataProvider provideTestConstruction
     */
    public function testConstruction($data)
    {
        $subject = new ArrayCollection($data);

        self::assertSame($data, $subject->getData(), 'Construction must work');
    }

    public function provideTestConstruction()
    {
        return [
            [
                [],
            ],
            [
                [1, 'a'],
            ],
        ];
    }

    //// BEGIN: testing AbstractCollection ////////////////////////////////////////////////////////////////////////

    public function testPsi()
    {
        $data    = [1, 'a'];
        $subject = new ArrayCollection($data);

        self::assertInstanceOf(Psi::class, $subject->psi(), 'psi() must work');

        self::assertSame($data, $subject->psi()->toArray(), 'psi() must work on the correct data');
    }

    /**
     * @param array $data
     *
     * @dataProvider provideTestCount
     */
    public function testCount(array $data)
    {
        $count   = count($data);
        $subject = new ArrayCollection($data);

        self::assertSame($count, $subject->count(), 'count() must work');

        self::assertCount($count, $subject, 'count($collection) must work');
    }

    public function provideTestCount()
    {
        return [
            [
                [],
            ],
            [
                [1],
            ],
            [
                [1, 2],
            ],
        ];
    }

    public function testFilter()
    {
        $subject = new ArrayCollection([1, 2, 3]);

        /** @noinspection PhpDeprecationInspection */
        $filtered = $subject->filter(function ($i) { return $i > 2; });

        self::assertSame([3], $filtered->getData(), 'filter() must work');
    }

    //// END: testing AbstractCollection //////////////////////////////////////////////////////////////////////////

    /**
     * @param array $base
     * @param mixed $append
     * @param array $expected
     *
     * @dataProvider provideTestAppend
     */
    public function testAppend($base, $append, $expected)
    {
        $subject = new ArrayCollection($base);
        $subject->append($append);

        self::assertSame($expected, $subject->getData(), 'append() must work');
    }

    /**
     * @param array $base
     * @param mixed $append
     * @param array $expected
     *
     * @dataProvider provideTestAppend
     */
    public function testAppendUsingBrackets($base, $append, $expected)
    {
        $subject   = new ArrayCollection($base);
        $subject[] = $append;

        self::assertSame($expected, $subject->getData(), 'appending by $collection[] = ... must work');
    }

    public function provideTestAppend()
    {
        return [
            [
                [],
                1,
                [1],
            ],
            [
                [1],
                1,
                [1, 1],
            ],
        ];
    }

    public function testGetIterator()
    {
        $data    = [1, 'a'];
        $subject = new ArrayCollection($data);

        $iterator = $subject->getIterator();

        self::assertInstanceOf(\ArrayIterator::class, $iterator, 'getIterator() must work');

        self::assertSame($data, $iterator->getArrayCopy(), 'getIterator() must return an iterator container the correct data');
    }

    /**
     * @param array $data
     * @param mixed $offset
     * @param bool  $expected
     *
     * @dataProvider provideTestOffsetExists
     */
    public function testOffsetExists($data, $offset, $expected)
    {
        $subject = new ArrayCollection($data);

        self::assertSame($expected, isset($subject[$offset]), 'offsetExists() must work');
    }

    public function provideTestOffsetExists()
    {
        return [
            // positive cases
            [
                [1],
                0,
                true,
            ],
            [
                [1, 2],
                1,
                true,
            ],
            [
                [4 => 'a'],
                4,
                true,
            ],
            [
                [4 => 'a', 'a' => 'a'],
                'a',
                true,
            ],
            [
                [1],
                '0',
                true,
            ],
            [
                [1, '1' => '1'],
                '1',
                true,
            ],
            // negative cases
            [
                [],
                null,
                false,
            ],
            [
                [],
                '',
                false,
            ],
            [
                [],
                0,
                false,
            ],
        ];
    }

    public function testOffsetGet()
    {
        $subject = new ArrayCollection([1, 2, 'a' => 'a']);

        // positive cases
        self::assertSame(1, $subject[0], '$subject[0] must work');
        self::assertSame(1, $subject['0'], '$subject["0"] must work');

        self::assertSame(2, $subject[1], '$subject[1] must work');
        self::assertSame(2, $subject['1'], '$subject["1"] must work');

        self::assertSame('a', $subject['a'], '$subject["a"] must work');

        // negative cases
        self::assertNull($subject[3], '$subject[3] must return null');
    }

    public function testOffsetSet()
    {
        $subject = new ArrayCollection([1, 2]);

        $subject[1]   = 3;
        $subject[2]   = 5;
        $subject[]    = 7;
        $subject['z'] = 'z';

        self::assertSame(
            [0 => 1, 1 => 3, 2 => 5, 3 => 7, 'z' => 'z'],
            $subject->getData(),
            'offsetSet() must work'
        );
    }

    public function testOffsetUnset()
    {
        $subject = new ArrayCollection([1, 2, 'z' => 'z', 3]);

        unset($subject[1], $subject['z']);

        self::assertSame(
            [0 => 1, 2 => 3],
            $subject->getData(),
            'offsetUnset() must work'
        );
    }
}
