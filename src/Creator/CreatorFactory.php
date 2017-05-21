<?php
/**
 * File was created 17.05.2016 06:36
 */

namespace PeekAndPoke\Component\Creator;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface CreatorFactory
{
    /**
     * @param \ReflectionClass $class
     *
     * @return Creator
     */
    public function create(\ReflectionClass $class);
}
