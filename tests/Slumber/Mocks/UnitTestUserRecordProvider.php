<?php
/**
 * File was created 12.10.2015 06:53
 */

namespace PeekAndPoke\Component\Slumber\Mocks;

use PeekAndPoke\Component\Slumber\Data\Addon\UserRecord\UserRecord;
use PeekAndPoke\Component\Slumber\Data\Addon\UserRecord\UserRecordProvider;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class UnitTestUserRecordProvider implements UserRecordProvider
{
    /**
     * @return UserRecord
     */
    public function getUserRecord()
    {
        return new UserRecord();
    }
}
