<?php
/**
 * File was created 12.10.2015 06:53
 */

namespace PeekAndPoke\Component\Slumber\Helper;

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
        return (new UserRecord())
            ->setName('UnitTestUser')
            ->setUserAgent('CLI')
            ->setRole('Admin')
            ->setIp('127.0.0.1')
            ->setUserId('UnitTest');
    }
}
