<?php

namespace Fuzzy\Cmd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DevEnvCommand extends BaseConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('dev:env')
            ->setDescription('Print the project context dev environment')
            ->setHelp("Print .env from the project docker dev directory");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectName = basename(FZKC_CONSOLE_BASE_PATH);

        $output->writeln(">>> Fzkc project [$projectName]");
        $output->writeln(">>> Project context dev environment <<<");

        //$output->setVerbosity(OutputInterface::VERBOSITY_NORMAL);

        foreach ($this->getContextEnvVars() as $envVarName => $envVarValue) {
            $output->writeln($envVarName . '=' . $envVarValue);
        }

        return Command::SUCCESS;
    }
}

