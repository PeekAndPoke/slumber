<?php
/**
 * File was created 07.10.2015 06:26
 */

namespace PeekAndPoke\Component\Slumber\Core\Codec;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface Awaker
{
    /**
     * @param mixed            $data
     * @param \ReflectionClass $cls
     *
     * @return mixed|null
     */
    public function awake($data, \ReflectionClass $cls);
}
