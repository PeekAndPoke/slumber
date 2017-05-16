<?php
/**
 * File was created 07.10.2015 06:25
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface Slumberer
{
    /**
     * @param mixed $subject
     *
     * @return array|string|null
     */
    public function slumber($subject);
}
