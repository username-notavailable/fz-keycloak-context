<?php

namespace Fuzzy\Fzkc\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class CastleNewCommand extends BaseConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('castle:new')
            ->setDescription('Install new castle')
            ->setHelp('Install a new castle into laravels directory')
            ->addArgument('dirname', InputArgument::REQUIRED, 'Fzkc castle name (laravels subdirectory name).')
            ->addArgument('type', InputArgument::REQUIRED, 'Fzkc castle type (package name like "fzkc/laravelweb" or similar).')
            ->addArgument('port', InputArgument::REQUIRED, 'Fzkc castle port (docker exposed port).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $castleName = $input->getArgument('dirname');
        $castleType = $input->getArgument('type');
        $castlePort = $input->getArgument('port');
        $castleDirectoryPath = $this->makeDirectoryPath(FZKC_CONSOLE_BASE_PATH, 'laravels', $castleName);

        if (is_dir($castleDirectoryPath)) {
            if (!$input->getOption('quiet')) {
                $output->writeln('!!! Fzkc castle directory "' . $castleName . '" already exists !!!');
            }

            return Command::FAILURE;
        }
        else {
            $resultCode = null;

            if (!$input->getOption('quiet')) {
                $output->writeln("\n>>> Fzkc install castle \"$castleName\" of type \"$castleType\"...\n");
            }

            chdir($this->makeDirectoryPath(FZKC_CONSOLE_BASE_PATH, 'laravels'));

            putenv("CASTLE_NAME=$castleName");
            putenv("CASTLE_PORT=$castlePort");

            system('composer create-project "' . $castleType . '" "' . $castleName . '"', $resultCode);

            return $resultCode === 0 ? Command::SUCCESS : Command::FAILURE;
        }

        if (!$input->getOption('quiet')) {
            $output->writeln("\n>>> Fzkg castle directory \"$castleName\" initialized <<<\n");
        }

        return Command::SUCCESS;
    }
}

