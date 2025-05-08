<?php

namespace Fuzzy\Fzkc\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class CastleDevStopCommand extends BaseConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('castle:dev:stop')
            ->setDescription('Stop a castle docker dev environment')
            ->setHelp('Run "docker compose down" from the castle _docker dev directory')
            ->addArgument('dirname', InputArgument::REQUIRED, 'Fzkc castle name (laravels subdirectory name).')
            ->addOption('docker', null, InputArgument::OPTIONAL, '"docker" arguments and options in "docker compose down" command.', '')
            ->addOption('down', null, InputArgument::OPTIONAL, '"up" arguments and options in "docker compose down" command.', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $castleName = $input->getArgument('dirname');

        $yamlFilePath = $this->makeFilePath(FZKC_CONSOLE_BASE_PATH, 'laravels',$castleName , '_docker', 'dev', 'compose.yaml');

        if (!is_file($yamlFilePath)) {
            if (!$input->getOption('quiet')) {
                $output->writeln('!!! Fzkc castle dev compose file "' . $yamlFilePath . '" not exists !!!');
            }

            return Command::FAILURE;
        }
        else {
            $returnCode = null;

            if (!$input->getOption('quiet')) {
                $output->writeln("\n>>> Fzkc castle \"$castleName\" docker dev environment [$yamlFilePath]...\n");
            }

            chdir(dirname($yamlFilePath));

            putenv('COMPOSE_PROJECT_NAME=' . basename(FZKC_CONSOLE_BASE_PATH) . '-' . $input->getArgument('dirname') .'-dev');

            system('docker ' . $input->getOption('docker') . ' compose down ' . $input->getOption('down'), $returnCode);

            return $returnCode === 0 ? Command::SUCCESS : Command::FAILURE;
        }
    }
}

