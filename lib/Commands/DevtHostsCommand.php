<?php

namespace Fuzzy\Cmd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Dallgoot\Yaml\Yaml;
use Badcow\DNS\Parser\Parser;

class DevtHostsCommand extends BaseConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('dev:hosts')
            ->setDescription('Print dev hosts')
            ->setHelp("Print a suggested hosts file");
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectName = basename(FZKC_CONSOLE_BASE_PATH);
        $contextEnvVars = $this->getContextEnvVars();
        $contextEnvVars['FZKC_PROJECT_NAME'] = $projectName;
        
        if (!$input->getOption('quiet')) {
            $output->writeln(">>> Fzkc project [$projectName]");
            $output->writeln(">>> hosts file <<<");
        }

        $output->writeln("### Hosts file [$projectName] ###");

        $yaml = Yaml::parseFile($this->makeFilePath(FZKC_CONSOLE_BASE_PATH, 'docker', 'dev', 'compose.yaml'), 0, 0);

        foreach ($yaml->include as $yamlServiceFileName) {
            $sYaml = Yaml::parseFile($this->makeFilePath(FZKC_CONSOLE_BASE_PATH, 'docker', 'dev', $yamlServiceFileName), 0, 0);
            $serviceName = basename($yamlServiceFileName, '.yaml');
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

            $output->writeln("$contextEnvVars[FZKC_NETWORK_GATEWAY_IP]\t$hostname\t# ports $ports");
        }

        $laravelsPath = $this->makeDirectoryPath(FZKC_CONSOLE_BASE_PATH, 'laravels');
        
        foreach (glob($laravelsPath . '*', GLOB_ONLYDIR) as $castle) {
            $castleName = basename($castle);
            $sYaml = Yaml::parseFile($this->makeFilePath(FZKC_CONSOLE_BASE_PATH, 'laravels', $castleName, '_docker', 'dev', 'compose.yaml'), 0, 0);
            $serviceName = $castleName . '-castle';
            $hostname = $sYaml->services->{$serviceName}->hostname;
            $ports = [];

            $castleEnvVars = $this->getCastleEnvVars($castleName);
            $castleEnvVars['FZKC_CASTLE_NAME'] = $castleName;

            foreach ($sYaml->services->{$serviceName}->ports as $port) {
                $ports[] = $port;
            }

            $ports = implode(' ', $ports);

            foreach ($contextEnvVars as $envVarName => $envVarValue) {
                $ports = preg_replace(['@\$\{' . $envVarName . '\}@'], $envVarValue, $ports);
                $hostname = preg_replace(['@\$\{' . $envVarName . '\}@'], $envVarValue, $hostname);
            }

            foreach ($castleEnvVars as $envVarName => $envVarValue) {
                $ports = preg_replace(['@\$\{' . $envVarName . '\}@'], $envVarValue, $ports);
                $hostname = preg_replace(['@\$\{' . $envVarName . '\}@'], $envVarValue, $hostname);
            }

            $output->writeln("$contextEnvVars[FZKC_NETWORK_GATEWAY_IP]\t$hostname\t# ports $ports");
        }

        $coreDnsZoneFilePath = $this->makeFilePath(FZKC_CONSOLE_BASE_PATH, 'docker', 'dev', 'coredns', 'conf', 'db.external.space');

        if (file_exists($coreDnsZoneFilePath)) {
            $file = file_get_contents($coreDnsZoneFilePath);
            $zone = Parser::parse('external.space.', $file);
            
            foreach ($contextEnvVars as $envVarName => $envVarValue) {
                $ports = preg_replace(['@\$\{' . $envVarName . '\}@'], $envVarValue, $ports);
            }

            foreach ($zone->getResourceRecords() as $record) {
                $data = $record->getRdata();

                if ($data instanceof \Badcow\DNS\Rdata\A) {
                    $hostname = trim($record->getName(), '.');

                    if (!in_array($hostname, ['a.external.space', 'b.external.space'])) {
                        $output->writeln("$contextEnvVars[FZKC_NETWORK_GATEWAY_IP]\t$hostname\t# nginx proxy_pass"); 
                    }                   
                }
            }
        }

        $output->writeln("###");

        return Command::SUCCESS;
    }
}

