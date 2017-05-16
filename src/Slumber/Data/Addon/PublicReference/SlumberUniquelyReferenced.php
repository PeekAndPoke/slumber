<?php
/**
 * File was created 05.10.2015 17:18
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon\PublicReference;

use PeekAndPoke\Component\Slumber\Annotation\Slumber;
use PeekAndPoke\Component\Slumber\SlumberDependencies;

/**
 * Use this to auto fill the public reference of a persisted object when it is saved.
 *
 * This is a variant of SlumberReferenced. The difference is that the Index on the field will be declared as unique.
 *
 * @see    SlumberReferenced
 * @see    SlumberDependencies
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
trait SlumberUniquelyReferenced
{
    /**
     * @var string
     *
     * @see SlumberDependencies
     *
     * @Slumber\AsString()
     *
     * @Slumber\Store\AsPublicReference(
     *      service = SlumberDependencies::PUBLIC_REFERENCE_GENERATOR,
     *      ofClass = SlumberDependencies::PUBLIC_REFERENCE_GENERATOR_CLASS,
     * )
     *
     * @Slumber\Store\Indexed(
     *     unique     = true,
     *     background = true,
     *     direction  = "ASC",
     *     sparse     = false,
     * )
     */
    protected $reference;

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return $this
     */
    public function setReference($reference)
    {
        $this->reference = $reference;

        return $this;
    }
}
