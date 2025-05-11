<?php

namespace Fuzzy\Cmd\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;


class CastleNewLaravelApiCommand extends BaseCastleConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('castle:new:laravel:api')
            ->setDescription('Install a new "fzkc/laravelapi" castle')
            ->setHelp('Install a new castle of type "fzkc/laravelapi" into laravels directory')
            ->addArgument('dirname', InputArgument::REQUIRED, 'Fzkc castle name (laravels subdirectory name).')
            ->addArgument('port', InputArgument::REQUIRED, 'Fzkc castle port (docker exposed port).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $castleNewInput = new ArrayInput([
            'command' => 'castle:new',
            'dirname'    => $input->getArgument('dirname'),
            'type'  => "fzkc/laravelapi",
            'port' => $input->getArgument('port')
        ]);

        $castleNewInput->setInteractive(false);

        $returnCode = $this->getApplication()->doRun($castleNewInput, $output);

        return $returnCode === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}

