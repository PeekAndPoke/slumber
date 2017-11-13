<?php
/**
 * Created by gerk on 13.11.17 21:40
 */

namespace PeekAndPoke\Component\Creator\Unit;

use PeekAndPoke\Component\Creator\CreateWithoutConstructor;
use PeekAndPoke\Component\Creator\Stubs\UnitTestCreatableWithoutCtor;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class CreateWithoutConstructorTest extends TestCase
{
    public function testCreate()
    {
        $subject = new CreateWithoutConstructor(new \ReflectionClass(UnitTestCreatableWithoutCtor::class));

        $this->assertSame(UnitTestCreatableWithoutCtor::class, $subject->getFqcn(), 'The fqcn must be correct');

        $this->assertInstanceOf(
            UnitTestCreatableWithoutCtor::class,
            $subject->create(),
            'Creation without constructor must work'
        );
    }
}
