<?php
/**
 * Created by IntelliJ IDEA.
 * User: gerk
 * Date: 20.04.17
 * Time: 00:47
 */

namespace PeekAndPoke\Component\Slumber\FrameworkIntegration\Cli;

use PeekAndPoke\Component\Slumber\Data\Migration\MigrationExecutor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MigrateListCommand extends Command
{
    /** @var MigrationExecutor */
    private $migrationExecutor;

    public function __construct(MigrationExecutor $migrationExecutor)
    {
        parent::__construct('slumber:migrate');

        $this->setDescription('List available and executed migrations');

        $this->migrationExecutor = $migrationExecutor;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->migrationExecutor->logStats();

        return 0;
    }
}
