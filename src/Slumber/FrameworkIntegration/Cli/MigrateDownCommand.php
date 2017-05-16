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
class MigrateDownCommand extends Command
{
    /** @var MigrationExecutor */
    private $executor;

    /**
     * @param MigrationExecutor $executor
     */
    public function __construct(MigrationExecutor $executor)
    {
        parent::__construct('slumber:migrate:down');

        $this->setDescription('Do downwards data storage migration (singular only)')
            ->addArgument('name', InputArgument::REQUIRED, 'The full name of the migration');

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

        $this->executor->down($name);

        return 0;
    }
}
