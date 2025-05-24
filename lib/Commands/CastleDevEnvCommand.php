<?php

namespace Fuzzy\Cmd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class CastleDevEnvCommand extends BaseConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('castle:dev:env')
            ->setDescription('Print the castle dev environment')
            ->setHelp("Print .env from the castle _docker dev directory")
            ->addArgument('dirname', InputArgument::REQUIRED, 'Fzkc castle name (laravels subdirectory name).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $castleName = $input->getArgument('dirname');
        $projectName = basename(FZKC_CONSOLE_BASE_PATH);

        $yamlFilePath = $this->makeFilePath(FZKC_CONSOLE_BASE_PATH, 'laravels', $castleName , '_docker', 'dev', 'compose.yaml');

        if (!is_file($yamlFilePath)) {
            $output->writeln('!!! Fzkc castle dev compose file "' . $yamlFilePath . '" not exists !!!');

            return Command::FAILURE;
        }
        else {
            $output->writeln(">>> Fzkc project [$projectName]");
            $output->writeln(">>> Fzkc castle [$castleName]");
            $output->writeln(">>> dev environment <<<");
    
            foreach ($this->getContextEnvVars() as $envVarName => $envVarValue) {
                $output->writeln($envVarName . '=' . $envVarValue);
            }

            foreach ($this->getCastleEnvVars($castleName) as $envVarName => $envVarValue) {
                $output->writeln($envVarName . '=' . $envVarValue);
            }
    
            return Command::SUCCESS;
        }
    }
}

