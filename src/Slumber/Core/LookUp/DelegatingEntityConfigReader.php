<?php
/**
 * File was created 11.02.2016 17:41
 */

namespace PeekAndPoke\Component\Slumber\Core\LookUp;

/**
 * @deprecated Will be removed in future version
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class DelegatingEntityConfigReader implements EntityConfigReader
{
    /** @var EntityConfigReader */
    protected $delegate;

    /**
     * DelegatingEntityConfigLookUp constructor.
     *
     * @param EntityConfigReader $delegate
     */
    public function __construct(EntityConfigReader $delegate)
    {
        $this->delegate = $delegate;
    }

    /**
     * @param \ReflectionClass $subject
     *
     * @return EntityConfig
     */
    public function getEntityConfig(\ReflectionClass $subject)
    {
        return $this->delegate->getEntityConfig($subject);
    }
}
