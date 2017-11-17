<?php
/**
 * Created by gerk on 17.11.17 06:51
 */

namespace PeekAndPoke\Component\Slumber\Unit\Core\Codec;

use PeekAndPoke\Component\Slumber\Core\Codec\NullSlumberer;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class NullSlumbererTest extends TestCase
{
    public function testSlumber()
    {
        $subject = new NullSlumberer();

        $this->assertNull($subject->slumber(null));
        $this->assertNull($subject->slumber('a'));
        $this->assertNull($subject->slumber([]));
        $this->assertNull($subject->slumber(['a' => 1]));
    }
}
