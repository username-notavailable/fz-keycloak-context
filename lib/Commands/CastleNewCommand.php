<?php

namespace Fuzzy\Cmd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;


class CastleNewCommand extends BaseCastleConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('castle:new')
            ->setDescription('Install a new castle')
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
        $projectName = basename(FZKC_CONSOLE_BASE_PATH);

        if (is_dir($castleDirectoryPath)) {
            $output->writeln('!!! Fzkc castle directory "' . $castleName . '" already exists !!!');

            return Command::FAILURE;
        }
        else {
            $returnCode = null;

            $output->writeln(">>> Fzkc project [$projectName]");
            $output->writeln(">>> NEW fzkc castle [$castleName]");
            $output->writeln(">>> Fzkc install castle \"$castleName\" of type \"$castleType\"...\n");

            chdir($this->makeDirectoryPath(FZKC_CONSOLE_BASE_PATH, 'laravels'));

            putenv('COMPOSE_PROJECT_NAME=' . $projectName);

            $this->setCastleEnvVars($projectName, $castleName, $castlePort);

            system('composer create-project "' . $castleType . '" "' . $castleName . '"', $returnCode);

            if ($returnCode === 0) {
                if ($input->getOption('quiet')) {
                    $output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
                }

                $commandsInputs = [];

                $commandsInputs[] = new ArrayInput([
                    'command' => 'replace:castle:name',
                    'dirname'    => $input->getArgument('dirname'),
                    'path'  => "_docker/dev/compose.yaml",
                    '-q' => true
                ]);

                $commandsInputs[] = new ArrayInput([
                    'command' => 'replace:castle:name',
                    'dirname'    => $input->getArgument('dirname'),
                    'path'  => "_docker/dev/.env",
                    '-q' => true
                ]);

                $commandsInputs[] = new ArrayInput([
                    'command' => 'replace:castle:host:port',
                    'dirname'    => $input->getArgument('dirname'),
                    'port' => $castlePort,
                    'path'  => "_docker/dev/.env",
                    '-q' => true
                ]);

                $commandsInputs[] = new ArrayInput([
                    'command' => 'replace:project:name',
                    'dirname'    => $input->getArgument('dirname'),
                    'path'  => "_docker/dev/.env",
                    '-q' => true
                ]);

                foreach ($commandsInputs as $commandInput) {
                    $commandInput->setInteractive(false);
                    $this->getApplication()->doRun($commandInput, $output);
                }

                $output->writeln("\n>>> Castle [$castleName] of type [$castleType] installed ($projectName.$castleName.space:$castlePort) <<<\n");

                return Command::SUCCESS;
            }

            return Command::FAILURE;
        }
    }
}

