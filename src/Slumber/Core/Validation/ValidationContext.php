<?php
/**
 * File was created 29.04.2016 17:35
 */

namespace PeekAndPoke\Component\Slumber\Core\Validation;

use Psr\Container\ContainerInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface ValidationContext
{
    /**
     * @return ContainerInterface
     */
    public function getProvider();

    /**
     * @return string
     */
    public function getAnnotationLocation();
}
