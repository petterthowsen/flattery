<?php

// src/Command/CreateUserCommand.php
namespace ThowsenMedia\Flattery\Console\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InstallPluginCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'plugin:install';

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Name of the plugin');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var ThowsenMedia\Flattery\Extending\PluginLoader $plugins
         */
        $plugins = flattery('plugins');
        
        $name = $input->getArgument('name');

        $error = $plugins->install($name);
        if ($error !== true) {
            if ($error == PluginLoader::ERR_NO_PLUGIN) {
                $output->writeln("Missing plugin file.");
            }else {
                $output->writeln("Missing plugin directory.");
            }

            return Command::FAILURE;
        }else {
            return Command::SUCCESS;
        }
    }
}