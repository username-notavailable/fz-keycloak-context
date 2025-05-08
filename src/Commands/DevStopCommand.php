<?php

namespace Fuzzy\Fzkc\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class DevStopCommand extends BaseConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('dev:stop')
            ->setDescription('Stop fzkc project dev environment')
            ->setHelp("Run \"docker compose down\" from fzkc docker dev directory")
            ->addOption('docker', null, InputArgument::OPTIONAL, '"docker" arguments and options in "docker compose down" command.', '')
            ->addOption('down', null, InputArgument::OPTIONAL, '"down" arguments and options in "docker compose down" command.', '');
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
                $output->writeln(">>> Stop fzkc docker dev environment [$yamlFilePath] <<<");
            }

            chdir(dirname($yamlFilePath));

            putenv('COMPOSE_PROJECT_NAME=' . basename(FZKC_CONSOLE_BASE_PATH));

            system('docker ' . $input->getOption('docker') . ' compose down ' . $input->getOption('down'), $returnCode);

            return $returnCode === 0 ? Command::SUCCESS : Command::FAILURE;
        }
    }
}

