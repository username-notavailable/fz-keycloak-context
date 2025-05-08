<?php

namespace Fuzzy\Fzkc\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class DevStartCommand extends BaseConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('dev:start')
            ->setDescription('Start fzkc project dev environment')
            ->setHelp("Run \"docker compose up\" from fzkc docker dev directory")
            ->addOption('docker', null, InputArgument::OPTIONAL, '"docker" arguments and options in "docker compose up" command.', '')
            ->addOption('up', null, InputArgument::OPTIONAL, '"up" arguments and options in "docker compose up" command.', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $yamlFilePath = $this->makeFilePath(FZKC_CONSOLE_BASE_PATH, 'docker', 'dev', 'compose.yaml');

        if (!is_file($yamlFilePath)) {
            if (!$input->getOption('quiet')) {
                $output->writeln('!!! Fzkc dev compose file "' . $yamlFilePath . '" not exists !!!');
            }

            return Command::FAILURE;
        }
        else {
            $returnCode = null;

            if (!$input->getOption('quiet')) {
                $output->writeln(">>> Start fzkc docker dev environment [$yamlFilePath]...");
            }

            chdir(dirname($yamlFilePath));

            putenv('COMPOSE_PROJECT_NAME=' . basename(FZKC_CONSOLE_BASE_PATH));

            system('docker ' . $input->getOption('docker') . ' compose up ' . $input->getOption('up'), $returnCode);

            return $returnCode === 0 ? Command::SUCCESS : Command::FAILURE;
        }
    }
}

