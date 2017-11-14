<?php
/**
 * Created by gerk on 14.11.17 16:39
 */

namespace PeekAndPoke\Component\Toolbox\Unit;

use PeekAndPoke\Component\Toolbox\ArrayUtil;
use PeekAndPoke\Component\Toolbox\Stubs\UnitTestTraversableClass;
use PHPUnit\Framework\TestCase;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class ArrayUtilTest extends TestCase
{
    /**
     * @param mixed $input
     * @param mixed $key
     * @param mixed $default
     * @param mixed $expected
     *
     * @dataProvider provideTestGet
     */
    public function testGet($input, $key, $default, $expected)
    {
        $result = ArrayUtil::get($input, $key, $default);

        $this->assertSame(
            $expected,
            $result,
            'get() must work correctly'
        );
    }

    /**
     * @return array
     */
    public function provideTestGet()
    {
        return [
            // negative cases
            [null, 'a', null, null],
            [null, 'a', 1, 1],
            [[], 'a', 1, 1],
            [[1], 'a', 1, 1],
            [['b' => 1], 'a', 1, 1],
            [new \ArrayObject(['b' => 1]), 'a', 1, 1],
            [new UnitTestTraversableClass(['b' => 1]), 'a', 1, 1],
            [[2, 4], 2, 1, 1],

            // positive cases
            [[2, 4], 0, 1, 2],
            [[2, 4], 1, 1, 4],
            [['a' => 2], 'a', 1, 2],
            [new \ArrayObject(['a' => 2]), 'a', 1, 2],
            [new UnitTestTraversableClass(['a' => 2]), 'a', 1, 2],
        ];
    }

    /**
     * @param mixed  $input
     * @param mixed  $path
     * @param mixed  $default
     * @param mixed  $expected
     * @param string $separator
     *
     * @dataProvider provideTestGetNested
     */
    public function testGetNested($input, $path, $default, $expected, $separator = '.')
    {
        $result = ArrayUtil::getNested($input, $path, $default, $separator);

        $this->assertSame(
            $expected,
            $result,
            'getNested() must work correctly'
        );
    }

    public function provideTestGetNested()
    {
        return [
            // negative cases
            [null, null, 1, 1],
            [[], null, 1, 1],
            [['a' => 2], null, 1, 1],
            [['a' => 2], 0, 1, 1],
            [['a' => 2], 'x', 1, 1],
            [['a' => 2], 'a.x', 1, 1],
            [['a' => 2], 'b.x', 1, 1],
            [['a' => 2], 'a.b.c', 1, 1],

            // positive cases
            [['a' => ['b' => 2]], 'a', 1, ['b' => 2]],
            [['a' => ['b' => 2]], 'a.b', 1, 2],
            [['a' => ['b' => ['c' => 2]]], 'a.b.c', 1, 2],
            [['a' => ['b' => ['c' => 2]]], 'a\\b\\c', 1, 2, '\\'],
        ];
    }

    /**
     * @param mixed $input
     * @param mixed $expected
     *
     * @dataProvider provideTestEnsureArray
     */
    public function testEnsureArray($input, $expected)
    {
        $result = ArrayUtil::ensureArray($input);

        $this->assertSame(
            $expected,
            $result,
            'ensureArray() must work correctly'
        );
    }

    public function provideTestEnsureArray()
    {
        return [
            [1, [1]],
            ['1', ['1']],
            [1.1, [1.1]],
            [$o = new \stdClass(), [$o]],

            [null, []],
            [[], []],
            [[1], [1]],
            [['a' => 1], ['a' => 1]],
            [[1, 2], [1, 2]],
            [['a' => 1, 2], ['a' => 1, 2]],
            [['a' => 1, 'b' => 2], ['a' => 1, 'b' => 2]],
            [[1, [2]], [1, [2]]],
            [['a' => 1, 'b' => [2]], ['a' => 1, 'b' => [2]]],

            [new \ArrayObject(), []],
            [new \ArrayObject([1]), [1]],
            [new \ArrayObject([1, 2]), [1, 2]],
            [new \ArrayObject([1, [2]]), [1, [2]]],
            [new \ArrayObject(['a' => 1, [2]]), ['a' => 1, [2]]],
            [new \ArrayObject(['a' => 1, 'b' => [2]]), ['a' => 1, 'b' => [2]]],

            [new UnitTestTraversableClass(), []],
            [new UnitTestTraversableClass([1]), [1]],
            [new UnitTestTraversableClass([1, 2]), [1, 2]],
            [new UnitTestTraversableClass([1, [2]]), [1, [2]]],
            [new UnitTestTraversableClass(['a' => 1, [2]]), ['a' => 1, [2]]],
            [new UnitTestTraversableClass(['a' => 1, 'b' => [2]]), ['a' => 1, 'b' => [2]]],
        ];
    }
}