<?php

namespace ThowsenMedia\Flattery\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListRoutesCommand extends Command
{

    protected static $defaultName = 'routes:list';

    protected function configure(): void
    {
        $this->setHelp("List all routes.");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $router = router();
        foreach ($router->getRoutes() as $route) {
            $output->writeln($route);
        }

        return Command::SUCCESS;
    }

}