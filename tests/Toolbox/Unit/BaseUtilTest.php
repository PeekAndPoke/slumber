<?php
/**
 * Created by gerk on 17.02.18 11:03
 */

namespace PeekAndPoke\Component\Toolbox\Unit;

use PeekAndPoke\Component\Toolbox\BaseUtil;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class BaseUtilTest extends TestCase
{

    /**
     * @param int $length
     *
     * @dataProvider provideTestCreateRandomToken
     */
    public function testCreateRandomToken($length)
    {
        $result = BaseUtil::createRandomToken($length);

        $this->assertSame($length, \strlen($result), 'createRandomToken() must work');
    }

    public function provideTestCreateRandomToken()
    {
        return [
            [0],
            [1],
            [2],
            [10],
            [100],
            [1000],
            [10000],
            [100000],
        ];
    }
}
