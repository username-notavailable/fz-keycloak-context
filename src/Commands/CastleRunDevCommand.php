<?php

namespace Fuzzy\Fzkc\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class CastleRunDevCommand extends BaseConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('castle:run:dev')
            ->setDescription('Start a castle docker dev environment')
            ->setHelp('Run "docker-compose up" from the castle _docker dev directory\n\t\"cmdline\" argument example: php console castle:run:dev --cmdline=\"-d --force-recreate\"')
            ->addArgument('dirname', InputArgument::REQUIRED, 'Fzkc castle name (laravels subdirectory name).')
            ->addOption('cmdline', null, InputArgument::OPTIONAL, '"docker-compose up" arguments and options.', '');
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

            system('docker-compose up ' . $input->getOption('cmdline'), $returnCode);

            return $returnCode === 0 ? Command::SUCCESS : Command::FAILURE;
        }
    }
}

