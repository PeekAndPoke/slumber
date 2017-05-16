<?php
declare(strict_types=1);
/**
 * File was created 30.09.2015 07:46
 */

namespace PeekAndPoke\Component\Slumber\Data\MongoDb\Types;

use PeekAndPoke\Component\Slumber\Core\Codec\Slumberer;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class SmallIntMapper extends \PeekAndPoke\Component\Slumber\Core\Codec\Property\SmallIntMapper
{
    /**
     * @param Slumberer $slumberer
     * @param mixed     $value
     *
     * @return int
     */
    public function slumber(Slumberer $slumberer, $value)
    {
        return (int) parent::slumber($slumberer, $value);
    }
}
