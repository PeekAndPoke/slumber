<?php
/**
 * Created by gerk on 13.11.17 21:40
 */

namespace PeekAndPoke\Component\Creator\Unit;

use PeekAndPoke\Component\Creator\CreateWithNonPublicConstructor;
use PeekAndPoke\Component\Creator\Stubs\UnitTestCreatableWithNonPublicConstructor;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class CreateWithNonPublicConstructorTest extends TestCase
{
    public function testCreate()
    {
        $subject = new CreateWithNonPublicConstructor(new \ReflectionClass(UnitTestCreatableWithNonPublicConstructor::class));

        $this->assertSame(UnitTestCreatableWithNonPublicConstructor::class, $subject->getFqcn(), 'The fqcn must be correct');

        $this->assertInstanceOf(
            UnitTestCreatableWithNonPublicConstructor::class,
            $subject->create(),
            'Creation with non public constructor must work'
        );
    }
}
