<?php
/**
 * Created by gerk on 13.11.17 21:40
 */

namespace PeekAndPoke\Component\Creator\Unit;

use PeekAndPoke\Component\Creator\CreateWithDefaultConstructor;
use PeekAndPoke\Component\Creator\Stubs\UnitTestCreatableWithParameterlessCtor;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class CreateWithConstructorTest extends TestCase
{
    public function testCreate()
    {
        $subject = new CreateWithDefaultConstructor(new \ReflectionClass(UnitTestCreatableWithParameterlessCtor::class));

        $this->assertSame(UnitTestCreatableWithParameterlessCtor::class, $subject->getFqcn(), 'The fqcn must be correct');

        $this->assertInstanceOf(
            UnitTestCreatableWithParameterlessCtor::class,
            $subject->create(),
            'Creation with constructor must work'
        );
    }
}
