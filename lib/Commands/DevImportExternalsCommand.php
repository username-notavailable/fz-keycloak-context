<?php

namespace Fuzzy\Cmd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Badcow\DNS\Classes;
use Badcow\DNS\Zone;
use Badcow\DNS\ZoneBuilder;
use Badcow\DNS\Rdata\Factory;
use Badcow\DNS\ResourceRecord;
use Badcow\DNS\Parser\Parser;

class DevImportExternalsCommand extends BaseConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('dev:import:externals')
            ->setDescription('Import the castles\' nginx templates')
            ->setHelp("Create nginx templates from castlesHostnames\' external file");
            //->addArgument('dirname', null, InputArgument::OPTIONAL, 'Fzkc castle name (laravels subdirectory name).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $projectName = basename(FZKC_CONSOLE_BASE_PATH);

        /*if ($input->hasArgument('dirname')) {
            $externalFilePath = $this->makeFilePath(FZKC_CONSOLE_BASE_PATH, 'laravels', $input->hasArgument('dirname'), '_docker', 'dev', 'data', 'external.conf.template');
            
            if (!is_file($externalFilePath)) {
                $output->writeln('!!! Fzkc dev external file "' . $externalFilePath . '" not exists !!!');
    
                return Command::FAILURE;
            }
        else {
        }*/

        if (!$input->getOption('quiet')) {
            $output->writeln(">>> Fzkc project [$projectName]");
        }

        $castlesHostnames = [];
        $laravelsPath = $this->makeDirectoryPath(FZKC_CONSOLE_BASE_PATH, 'laravels');
        $contextEnvVars = $this->getContextEnvVars();

        foreach (glob($laravelsPath . '*', GLOB_ONLYDIR) as $castle) {
            $castleName = basename($castle);
            $externalFilePath = $this->makeFilePath($castle, '_docker', 'dev', 'data', 'external.conf.template');

            if (file_exists($externalFilePath)) {
                $castleEnvVars = $this->getCastleEnvVars($castleName);
                $castlePort = $castleEnvVars['FZKC_CASTLE_PORT'];
                $castlesHostnames[] = $castleName . '-' . $projectName . '.external.space';
                $outputTemplatePath = $this->makeFilePath(FZKC_CONSOLE_BASE_PATH, 'docker', 'dev', 'nginx', 'etc', 'nginx', 'templates', $castleName . '-' . $projectName . '.external.space.conf.template');
                copy($externalFilePath, $outputTemplatePath);

                file_put_contents($outputTemplatePath, preg_replace(['@{%% FZKC_PROJECT_NAME %%}@', '@{%% FZKC_CASTLE_NAME %%}@', '@{%% FZKC_CASTLE_PORT %%}@'], [$projectName, $castleName, $castlePort], file_get_contents($outputTemplatePath)));

                if (!$input->getOption('quiet')) {
                    $output->writeln(">>> Castle [$castleName] external template imported <<<");
                }
            }
            else {
                if (!$input->getOption('quiet')) {
                    $output->writeln(">>> Castle [$castleName] external template not imported <<<");
                }
            }
        }

        $coreDnsZoneFilePath = $this->makeFilePath(FZKC_CONSOLE_BASE_PATH, 'docker', 'dev', 'coredns', 'conf', 'db.external.space');

        if (!file_exists($coreDnsZoneFilePath)) {
            $serial = 1;
        }
        else {
            $file = file_get_contents($coreDnsZoneFilePath);
            $zone = Parser::parse('external.space.', $file);
            $serial = 1;

            foreach ($zone->getResourceRecords() as $record) {
                $data = $record->getRdata();

                if ($data instanceof \Badcow\DNS\Rdata\SOA) {
                    $serial = $data->getSerial();
                    $serial = $serial + 1;
                    
                    break;
                }
            }
        }

        $zone = $this->getExternalFileSign($contextEnvVars['FZKC_NETWORK_DNS_IP'], $serial);
        
        foreach ($castlesHostnames as $castleHostname) {
            $a = new ResourceRecord();
            $a->setName($castleHostname . '.');
            $a->setRdata(Factory::A($contextEnvVars['FZKC_NETWORK_GATEWAY_IP']));

            $zone->addResourceRecord($a);
            
            if (!$input->getOption('quiet')) {
                $output->writeln(">>> Host [$castleHostname] added to db.external.space <<<");
            }
        }

        file_put_contents($coreDnsZoneFilePath, ZoneBuilder::build($zone));

        return Command::SUCCESS;
    }

    protected function getExternalFileSign(string $dnsIP, string $serial) : Zone
    {
        $zone = new Zone('external.space.');
        $zone->setDefaultTtl(3600);

        $soa = new ResourceRecord();
        $soa->setName('@');
        $soa->setClass(Classes::INTERNET);
        $soa->setRdata(Factory::Soa(
            'external.space.',
            'post.external.space.',
            $serial,
            7200,
            3600,
            1209600,
            3600
        ));

        $ns1 = new ResourceRecord();
        $ns1->setName('@');
        $ns1->setClass(Classes::INTERNET);
        $ns1->setRdata(Factory::Ns('a.external.space.'));

        $ns2 = new ResourceRecord;
        $ns2->setName('@');
        $ns2->setClass(Classes::INTERNET);
        $ns2->setRdata(Factory::Ns('b.external.space.'));

        $a = new ResourceRecord();
        $a->setName('a.external.space.');
        $a->setRdata(Factory::A($dnsIP));
        $a->setComment('This is a local ip.');

        $b = new ResourceRecord();
        $b->setName('b.external.space.');
        $b->setRdata(Factory::A($dnsIP));
        $b->setComment('This is a local ip.');

        $zone->addResourceRecord($soa);
        $zone->addResourceRecord($ns1);
        $zone->addResourceRecord($ns2);
        $zone->addResourceRecord($a);
        $zone->addResourceRecord($b);

        return $zone;
    }
}

