<?php
/**
 * Created by gerk on 09.11.17 16:25
 */

namespace PeekAndPoke\Component\Slumber\Functional\Core;

use PeekAndPoke\Component\Slumber\Helper\TestHelper;
use PeekAndPoke\Component\Slumber\Stubs\UnitTestClassInheritingAPrivateProperty;
use PHPUnit\Framework\TestCase;


/**
 *
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class SlumberPrivatePropertiesOfBaseClassTest extends TestCase
{
    public function testIt()
    {
        $subject = new UnitTestClassInheritingAPrivateProperty();

        $subject->setOther('other');
        $subject->setId('id');

        $codec = TestHelper::getInstance()->getArrayCodec();

        $result = $codec->slumber($subject);

        $this->assertEquals(
            ['id' => 'id', 'other' => 'other', ],
            $result
        );
    }
}
