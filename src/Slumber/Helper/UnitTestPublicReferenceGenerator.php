<?php
/**
 * File was created 12.10.2015 06:53
 */

namespace PeekAndPoke\Component\Slumber\Helper;

use PeekAndPoke\Component\Slumber\Data\Addon\PublicReference\PublicReferenceGenerator;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class UnitTestPublicReferenceGenerator implements PublicReferenceGenerator
{
    private $used = [];

    /**
     * @param mixed $subject The object to create a public unique reference for
     *
     * @return string
     */
    public function create($subject)
    {
        $reflect = new \ReflectionClass($subject);

        do {
            $reference = $reflect->getName() . '@' . mt_rand();
        } while (\in_array($reference, $this->used, true));

        $this->used[] = $reference;

        return $reference;
    }
}
