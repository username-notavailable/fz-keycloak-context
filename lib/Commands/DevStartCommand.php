<?php

namespace Fuzzy\Cmd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Dallgoot\Yaml\Yaml;

class DevStartCommand extends BaseConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('dev:start')
            ->setDescription('Start the project context dev environment')
            ->setHelp("Run \"docker compose up\" from project docker dev directory")
            ->addOption('docker', null, InputArgument::OPTIONAL, '"docker" arguments and options in "docker compose up" command.', '')
            ->addOption('up', null, InputArgument::OPTIONAL, '"up" arguments and options in "docker compose up" command.', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $yamlFilePath = $this->makeFilePath(FZKC_CONSOLE_BASE_PATH, 'docker', 'dev', 'compose.yaml');
        $projectName = basename(FZKC_CONSOLE_BASE_PATH);
        $contextEnvVars = $this->getContextEnvVars();

        if (!is_file($yamlFilePath)) {
            $output->writeln('!!! Fzkc dev compose file "' . $yamlFilePath . '" not exists !!!');

            return Command::FAILURE;
        }
        else {
            $returnCode = null;

            if (!$input->getOption('quiet')) {
                $output->writeln(">>> Fzkc project [$projectName]\n");
            }

            $yaml = Yaml::parseFile($yamlFilePath, 0, 0);
            $label_l = 32;

            foreach ($yaml->include as $yamlServiceFileName) {
                $sYaml = Yaml::parseFile($this->makeFilePath(FZKC_CONSOLE_BASE_PATH, 'docker', 'dev', $yamlServiceFileName), 0, 0);
                
                $serviceName = basename($yamlServiceFileName, '.yaml');

                if (!$input->getOption('quiet')) {
                    $hostname = $sYaml->services->{$serviceName}->hostname;
                    $ports = [];

                    foreach ($sYaml->services->{$serviceName}->ports as $port) {
                        $ports[] = $port;
                    }

                    $ports = implode(' ', $ports);

                    foreach ($contextEnvVars as $envVarName => $envVarValue) {
                        $ports = preg_replace(['@\$\{' . $envVarName . '\}@'], $envVarValue, $ports);
                        $hostname = preg_replace(['@\$\{' . $envVarName . '\}@'], $envVarValue, $hostname);
                    }

                    $output->writeln(sprintf(">>> %'.-40s %-s", $hostname, $ports));
                }

                $containerName = $sYaml->services->{$serviceName}->container_name;

                foreach ($contextEnvVars as $envVarName => $envVarValue) {
                    $containerName = preg_replace(['@\$\{' . $envVarName . '\}@'], $envVarValue, $containerName);
                }

                $t_label_l = strlen($containerName);
                
                if ($t_label_l > $label_l) {
                    $label_l = $t_label_l;
                }
            }

            if (!$input->getOption('quiet')) {
                $output->writeln("\n>>> Start project context dev environment [$yamlFilePath]...\n");
            }

            chdir(dirname($yamlFilePath));

            putenv('COMPOSE_PROJECT_NAME=' . $projectName);
            putenv('FZKC_PROJECT_NAME=' . $projectName);

            putenv('COLUMNS=' . (intval(getenv('COLUMNS') ?: 80) - $label_l));
            putenv('LINES=' . intval(getenv('LINES') ?: 24));
            putenv('TERM=' . getenv('TERM') ?: '');

            system('docker ' . $input->getOption('docker') . ' compose up ' . $input->getOption('up'), $returnCode);

            return $returnCode === 0 ? Command::SUCCESS : Command::FAILURE;
        }
    }
}

