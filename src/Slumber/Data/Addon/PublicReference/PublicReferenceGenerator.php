<?php
/**
 * File was created 08.10.2015 19:11
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon\PublicReference;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface PublicReferenceGenerator
{
    /**
     * @param mixed $subject The object to create a public unique reference for
     *
     * @return null|string
     */
    public function create($subject) : ?string;
}
