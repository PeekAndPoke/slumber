<?php
/**
 * File was created 05.07.2016 17:06
 */

namespace PeekAndPoke\Component\Slumber\Data\Migration;

use PeekAndPoke\Component\Slumber\Data\Cursor;
use PeekAndPoke\Component\Slumber\Data\EntityRepository;
use PeekAndPoke\Component\Slumber\Data\Migration\DomainModel\ExecutedMigration;


/**
 * @method ExecutedMigration[]|Cursor find(array $query = null)
 * @method ExecutedMigration|null     findOne(array $query = null)
 * @method ExecutedMigration|null     findById($id)
 * @method ExecutedMigration|null     findByReference($reference)
 *
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MigrationRepositoryImpl extends EntityRepository implements MigrationRepository
{
    /**
     * @return ExecutedMigration[]|Cursor
     */
    public function findAll()
    {
        return $this->find()->sort(['_id' => 1]);
    }

    /**
     * @param string $name
     *
     * @return ExecutedMigration|Cursor
     */
    public function findOneByName($name)
    {
        return $this->findOne([
            'name' => $name,
        ]);
    }

    /**
     * @param string $name
     *
     * @return int
     */
    public function countByName($name)
    {
        return $this->find([
            'name' => $name,
        ])->count();
    }

    /**
     * @param ExecutedMigration $executedMigration
     */
    public function store(ExecutedMigration $executedMigration)
    {
        $this->save($executedMigration);
    }

    /**
     * @param ExecutedMigration $executedMigration
     */
    public function delete(ExecutedMigration $executedMigration)
    {
        $this->remove($executedMigration);
    }
}
