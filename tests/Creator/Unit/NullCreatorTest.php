<?php
/**
 * Created by gerk on 13.11.17 21:36
 */

namespace PeekAndPoke\Component\Creator\Unit;

use PeekAndPoke\Component\Creator\NullCreator;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class NullCreatorTest extends TestCase
{
    public function testCreate()
    {
        $subject = new NullCreator();

        $this->assertNull($subject->create('something'));
    }
}
