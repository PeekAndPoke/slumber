<?php
/**
 * File was created 05.07.2016 16:34
 */

namespace PeekAndPoke\Component\Slumber\Data\Migration;

use PeekAndPoke\Component\Slumber\Data\Migration\DomainModel\ExecutedMigration;
use PeekAndPoke\Component\Slumber\Data\Migration\Exception\MigrationException;
use PeekAndPoke\Component\Toolbox\ExceptionUtil;
use Psr\Log\LoggerInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MigrationExecutor
{
    /** @var MigrationRepository */
    private $repository;

    /** @var Migration[] */
    private $migrations = [];
    /** @var LoggerInterface */
    private $logger;

    /**
     * MigrationExecutor constructor.
     *
     * @param MigrationRepository $repository
     * @param LoggerInterface     $logger
     */
    public function __construct(MigrationRepository $repository, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->logger     = $logger;
    }

    /**
     * @param Migration $migration
     *
     * @return $this
     */
    public function registerMigration(Migration $migration)
    {
        $name = $this->normalizeName($migration);

        if (isset($this->migrations[$name])) {
            throw new \InvalidArgumentException("Migration $name is already registered");
        }

        $this->migrations[$name] = $migration;

        return $this;
    }

    public function logStats()
    {
        $all  = $this->repository->findAll();
        $open = array_keys($this->migrations);

        $this->logger->info('===== EXECUTED MIGRATIONS =====');

        foreach ($all as $item) {
            $this->logger->info(str_pad($item->getName(), 80, ' ', STR_PAD_RIGHT) . ' on ' . $item->getCreatedAt()->format('c'));

            $open = array_filter(
                $open,
                function ($name) use ($item) {
                    return $name !== $item->getName();
                }
            );
        }

        $this->logger->info('===== AVAILABLE MIGRATIONS =====');

        foreach ($open as $name) {
            $this->logger->info("$name  (created {$this->migrations[$name]->getCreationDate()->format()})");
        }
    }

    /**
     * @param string $name
     */
    public function up($name)
    {
        if (! isset($this->migrations[$name])) {
            throw new MigrationException("There is no migration registered with the name '$name'");
        }

        if ($this->repository->countByName($name) > 0) {
            throw new MigrationException("Cannot 'up' the migration '$name' since it was already executed");
        }

        $migration = $this->migrations[$name];

        try {
            $this->logger->info('Migrating ' . $name . ' UP now');

            $executed = $migration->up();
            $executed = $executed ?: new ExecutedMigration();
            $executed->setName($name);

            $this->repository->store($executed);
            $this->logger->info('... done with migrating ' . $name . ' UP');

        } catch (\Exception $e) {
            ExceptionUtil::log($this->logger, $e, '[SlumberMigration] Error running migration ' . $name);

            throw new MigrationException('Error running migration ' . $name, 0, $e);
        }

    }

    /**
     */
    public function upAll()
    {
        $numExecuted = 0;

        foreach ($this->migrations as $name => $migration) {

            if ($this->repository->countByName($name) === 0) {
                $this->up($name);
                $numExecuted++;
            }
        }

        $this->logger->info("Done migrating. Executed $numExecuted migrations");
    }

    /**
     * @param string $name
     */
    public function down($name)
    {
        if (! isset($this->migrations[$name])) {
            throw new MigrationException("There is no migration registered with the name '$name'");
        }

        $executed = $this->repository->findOneByName($name);

        if ($executed === null) {
            throw new MigrationException("Cannot 'down' the migration '$name' since it was not executed yet");
        }

        $migration = $this->migrations[$name];

        try {
            $this->logger->info('Migrating ' . $name . ' DOWN now');

            $migration->down($executed);
            $this->repository->delete($executed);

            $this->logger->info('... done with migrating ' . $name . ' DOWN');
        } catch (\Exception $e) {
            ExceptionUtil::log($this->logger, $e, '[SlumberMigration] Error running migration ' . $name);

            throw new MigrationException('Error running migration ' . $name, 0, $e);
        }
    }

    /**
     * @param Migration $migration
     *
     * @return string
     */
    private function normalizeName(Migration $migration)
    {
        return $migration->getName();
    }
}
