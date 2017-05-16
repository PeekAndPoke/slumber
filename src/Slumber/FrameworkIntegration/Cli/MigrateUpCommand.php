<?php

namespace PeekAndPoke\Component\Slumber\FrameworkIntegration\Cli;

use PeekAndPoke\Component\Slumber\Data\Migration\MigrationExecutor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Karsten J. Gerber <kontakt@karsten-gerber.de>
 */
class MigrateUpCommand extends Command
{
    /** @var MigrationExecutor */
    private $executor;

    /**
     * @param MigrationExecutor $executor
     */
    public function __construct(MigrationExecutor $executor)
    {
        parent::__construct('slumber:migrate:up');

        $this->setDescription('Do upwards data migration (all at once or a one single one)')
            ->addArgument('name', InputArgument::OPTIONAL, 'The full name of a single migration. If none is given all will be executed');

        $this->executor = $executor;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument('name');

        if (empty($name)) {
            $this->executor->upAll();
        } else {
            $this->executor->up($name);
        }

        return 0;
    }
}
