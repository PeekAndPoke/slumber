<?php
/**
 * File was created 08.10.2015 21:23
 */

namespace PeekAndPoke\Component\Slumber;

use PeekAndPoke\Component\Slumber\Data\Addon\PublicReference\PublicReferenceGenerator;
use PeekAndPoke\Component\Slumber\Data\Addon\UserRecord\UserRecordProvider;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class SlumberDependencies
{
    /**
     * Key of the service for providing public references
     */
    const PUBLIC_REFERENCE_GENERATOR = 'slumber.public_reference_generator';
    /**
     * The expected type of the service
     *
     * @see \PeekAndPoke\Component\Slumber\Data\Addon\PublicReference\PublicReferenceGenerator
     */
    const PUBLIC_REFERENCE_GENERATOR_CLASS = PublicReferenceGenerator::class;

    /**
     * Key of the service providing info about the currently logged in user
     */
    const USER_RECORD_PROVIDER = 'slumber.user_record_provider';
    /**
     * The expected type of the service
     *
     * @see UserRecordProvider
     */
    const USER_RECORD_PROVIDER_CLASS = UserRecordProvider::class;
}
