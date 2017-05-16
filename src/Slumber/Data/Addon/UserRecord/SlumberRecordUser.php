<?php
/**
 * File was created 26.04.2016 07:24
 */

namespace PeekAndPoke\Component\Slumber\Data\Addon\UserRecord;

use PeekAndPoke\Component\Slumber\Annotation\Slumber;
use PeekAndPoke\Component\Slumber\SlumberDependencies;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
trait SlumberRecordUser
{
    /**
     * @var UserRecord
     *
     * @see SlumberDependencies
     *
     * @Slumber\AsObject(UserRecord::class)
     *
     * @Slumber\Store\AsUserRecord(
     *     service=SlumberDependencies::USER_RECORD_PROVIDER,
     *     ofClass=SlumberDependencies::USER_RECORD_PROVIDER_CLASS,
     * )
     */
    protected $createdBy;

    /**
     * @return UserRecord
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
