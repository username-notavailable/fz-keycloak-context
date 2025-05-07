<?php

namespace Fuzzy\Fzkc\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class RunDevCommand extends BaseConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('run:dev')
            ->setDescription('Start fzkc docker dev environment')
            ->setHelp('Run "docker-compose up" from fzkc docker dev directory');
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
            $resultCode = null;

            if (!$input->getOption('quiet')) {
                $output->writeln("\n>>> Fzkc docker dev environment [$yamlFilePath]...\n");
            }

            chdir(dirname($yamlFilePath));

            system('docker-compose up', $resultCode);

            return $resultCode === 0 ? Command::SUCCESS : Command::FAILURE;
        }
    }
}

