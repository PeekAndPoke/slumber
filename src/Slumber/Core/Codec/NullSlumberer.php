<?php
/**
 * File was created 07.10.2015 06:33
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class NullSlumberer implements Slumberer
{
    /**
     * @param mixed $subject
     *
     * @return null
     */
    public function slumber($subject)
    {
        return null;
    }
}
