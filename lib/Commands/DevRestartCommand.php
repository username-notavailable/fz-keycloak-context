<?php

namespace Fuzzy\Cmd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

class DevRestartCommand extends BaseConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('dev:restart')
            ->setDescription('Restart the project context dev environment')
            ->setHelp("Run \"docker compose down\" then \"docker compose up\" from project docker dev directory")
            ->addOption('docker-down', null, InputArgument::OPTIONAL, '"docker" arguments and options in "docker compose down" command.', '')
            ->addOption('down', null, InputArgument::OPTIONAL, '"down" arguments and options in "docker compose down" command.', '')
            ->addOption('docker-up', null, InputArgument::OPTIONAL, '"docker" arguments and options in "docker compose up" command.', '')
            ->addOption('up', null, InputArgument::OPTIONAL, '"up" arguments and options in "docker compose up" command.', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandsInputs = [];

        $commandsInputs[] = new ArrayInput([
            'command' => 'dev:stop',
            '--docker' => $input->getOption('docker-down'),
            '--down' => $input->getOption('down')
        ]);

        $commandsInputs[] = new ArrayInput([
            'command' => 'dev:start',
            '--docker' => $input->getOption('docker-up'),
            '--up' => $input->getOption('up')
        ]);

        foreach ($commandsInputs as $commandInput) {
            $commandInput->setInteractive(false);
            $this->getApplication()->doRun($commandInput, $output);
        }

        return Command::SUCCESS;
    }
}

