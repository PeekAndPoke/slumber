<?php
/**
 * Created by gerk on 14.11.17 17:19
 */

namespace PeekAndPoke\Component\Emitter\Unit;

use PeekAndPoke\Component\Toolbox\BaseUtil;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class BaseUtilTest extends TestCase
{
    public function testNoop()
    {
        BaseUtil::noop();
        BaseUtil::noop(1);
        BaseUtil::noop(1, 2);

        $this->assertTrue(true);
    }
}
