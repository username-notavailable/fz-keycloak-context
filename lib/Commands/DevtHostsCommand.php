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
        
        $output->writeln(">>> Fzkc project [$projectName]");
        $output->writeln(">>> hosts file <<<");

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

            $ports = $this->envSubst(implode(' ', $ports), $contextEnvVars);
            $hostname = $this->envSubst($hostname, $contextEnvVars);

            $output->writeln(sprintf("%-20s %-45s # ports %-10s", $contextEnvVars['FZKC_NETWORK_GATEWAY_IP'], $hostname, $ports));
        }

        $laravelsPath = $this->makeDirectoryPath(FZKC_CONSOLE_BASE_PATH, 'laravels');
        
        foreach (glob($laravelsPath . '*', GLOB_ONLYDIR) as $castle) {
            $castleName = basename($castle);
            $sYaml = Yaml::parseFile($this->makeFilePath(FZKC_CONSOLE_BASE_PATH, 'laravels', $castleName, '_docker', 'dev', 'compose.yaml'), 0, 0);
            $serviceName = $castleName . '-castle';
            $hostname = $sYaml->services->{$serviceName}->hostname;
            $ports = [];

            $castleEnvVars = $this->getCastleEnvVars($castleName);

            foreach ($sYaml->services->{$serviceName}->ports as $port) {
                $ports[] = $port;
            }

            $ports = $this->envSubst(implode(' ', $ports), $contextEnvVars, $castleEnvVars);
            $hostname = $this->envSubst($hostname, $contextEnvVars, $castleEnvVars);

            $output->writeln(sprintf("%-20s %-45s # ports %-10s", $contextEnvVars['FZKC_NETWORK_GATEWAY_IP'], $hostname, $ports));
        }

        $coreDnsZoneFilePath = $this->makeFilePath(FZKC_CONSOLE_BASE_PATH, 'docker', 'dev', 'coredns', 'conf', 'db.' . $projectName . '.external.space');

        if (file_exists($coreDnsZoneFilePath)) {
            $file = file_get_contents($coreDnsZoneFilePath);
            $zone = Parser::parse($projectName . '.external.space.', $file);
            
            foreach ($zone->getResourceRecords() as $record) {
                $data = $record->getRdata();

                if ($data instanceof \Badcow\DNS\Rdata\A) {
                    $hostname = trim($record->getName(), '.');

                    if (!in_array($hostname, ['a.' . $projectName . '.external.space', 'b.' . $projectName . '.external.space'])) {
                        $output->writeln(sprintf("%-20s %-45s # nginx proxy_pass", $contextEnvVars['FZKC_NETWORK_GATEWAY_IP'], $hostname));
                    }                   
                }
            }
        }

        $output->writeln("###");

        return Command::SUCCESS;
    }
}

