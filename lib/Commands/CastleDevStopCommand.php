<?php

namespace Fuzzy\Cmd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class CastleDevStopCommand extends BaseCastleConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('castle:dev:stop')
            ->setDescription('Stop the castle dev environment')
            ->setHelp('Run "docker compose down" from the castle _docker dev directory')
            ->addArgument('dirname', InputArgument::REQUIRED, 'Fzkc castle name (laravels subdirectory name).')
            ->addOption('docker', null, InputArgument::OPTIONAL, '"docker" arguments and options in "docker compose down" command.', '')
            ->addOption('down', null, InputArgument::OPTIONAL, '"down" arguments and options in "docker compose down" command.', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $castleName = $input->getArgument('dirname');
        $projectName = basename(FZKC_CONSOLE_BASE_PATH);

        $yamlFilePath = $this->makeFilePath(FZKC_CONSOLE_BASE_PATH, 'laravels',$castleName , '_docker', 'dev', 'compose.yaml');

        if (!is_file($yamlFilePath)) {
            $output->writeln('!!! Fzkc castle dev compose file "' . $yamlFilePath . '" not exists !!!');

            return Command::FAILURE;
        }
        else {
            $returnCode = null;

            $output->writeln(">>> Fzkc project [$projectName]");
            $output->writeln(">>> Fzkc castle [$castleName]");
            $output->writeln(">>> Stop castle dev environment [$yamlFilePath]...\n");

            chdir(dirname($yamlFilePath));

            putenv('COMPOSE_PROJECT_NAME=' . $projectName);
            
            $this->setCastleEnvVars($projectName, $castleName, null);

            system('docker ' . $input->getOption('docker') . ' compose down ' . $input->getOption('down'), $returnCode);

            return $returnCode === 0 ? Command::SUCCESS : Command::FAILURE;
        }
    }
}

