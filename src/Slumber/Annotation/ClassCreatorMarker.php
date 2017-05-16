<?php
/**
 * File was created 30.09.2015 09:44
 */

namespace PeekAndPoke\Component\Slumber\Annotation;

use PeekAndPoke\Component\Creator\Creator;
use PeekAndPoke\Component\Creator\CreatorFactory;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface ClassCreatorMarker extends ClassMarker
{
    /**
     * @param CreatorFactory $factory
     *
     * @return Creator
     */
    public function getCreator(CreatorFactory $factory);
}
