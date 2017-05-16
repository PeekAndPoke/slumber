<?php
/**
 * File was created 05.07.2016 16:35
 */

namespace PeekAndPoke\Component\Slumber\Data\Migration;

use PeekAndPoke\Component\Slumber\Data\Migration\DomainModel\ExecutedMigration;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
interface MigrationRepository
{
    /**
     * @return ExecutedMigration[]
     */
    public function findAll();

    /**
     * @param string $name
     *
     * @return ExecutedMigration
     */
    public function findOneByName($name);

    /**
     * @param string $name
     *
     * @return int
     */
    public function countByName($name);

    /**
     * @param ExecutedMigration $executedMigration
     */
    public function store(ExecutedMigration $executedMigration);

    /**
     * @param ExecutedMigration $executedMigration
     */
    public function delete(ExecutedMigration $executedMigration);
}
