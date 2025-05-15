<?php

namespace Fuzzy\Cmd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;

class CastleDevRestartCommand extends BaseConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('castle:dev:restart')
            ->setDescription('Restart the castle dev environment')
            ->setHelp("Run \"docker compose down\" then \"docker compose up\" from the castle _docker dev directory")
            ->addArgument('dirname', InputArgument::REQUIRED, 'Fzkc castle name (laravels subdirectory name).')
            ->addOption('docker-down', null, InputArgument::OPTIONAL, '"docker" arguments and options in "docker compose down" command.', '')
            ->addOption('down', null, InputArgument::OPTIONAL, '"down" arguments and options in "docker compose down" command.', '')
            ->addOption('docker-up', null, InputArgument::OPTIONAL, '"docker" arguments and options in "docker compose up" command.', '')
            ->addOption('up', null, InputArgument::OPTIONAL, '"up" arguments and options in "docker compose up" command.', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $yamlFilePath = $this->makeFilePath(FZKC_CONSOLE_BASE_PATH, 'laravels', $input->getArgument('dirname') , '_docker', 'dev', 'compose.yaml');

        if (!is_file($yamlFilePath)) {
            $output->writeln('!!! Fzkc castle dev compose file "' . $yamlFilePath . '" not exists !!!');

            return Command::FAILURE;
        }
        else {
            $commandsInputs = [];

            $commandsInputs[] = new ArrayInput([
                'command' => 'castle:dev:stop',
                'dirname' => $input->getArgument('dirname'),
                '--docker' => $input->getOption('docker-down'),
                '--down' => $input->getOption('down')
            ]);

            $commandsInputs[] = new ArrayInput([
                'command' => 'castle:dev:start',
                'dirname' => $input->getArgument('dirname'),
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
}

