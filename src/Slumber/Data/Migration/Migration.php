<?php
/**
 * File was created 05.07.2016 16:46
 */

namespace PeekAndPoke\Component\Slumber\Data\Migration;

use PeekAndPoke\Component\Slumber\Data\Migration\DomainModel\ExecutedMigration;
use PeekAndPoke\Types\LocalDate;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface Migration
{
    /**
     * @return string
     */
    public function getName();

    /**
     * This function must return the date when the migration was created.
     *
     * This value will be used for sorting all available migrations.
     * The migrations will be executed by this sort order.
     *
     * <code>
     * return new LocalDate('2017-04-10T10:00:00', 'Europe/Berlin');
     * </code>
     *
     * @return LocalDate
     */
    public function getCreationDate();

    /**
     * @return ExecutedMigration
     */
    public function up();

    /**
     * @param ExecutedMigration $executedMigration
     */
    public function down(ExecutedMigration $executedMigration);
}
