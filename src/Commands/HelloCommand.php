<?php

namespace Fuzzy\Fzkc\Commands;

// Importing the Command base class
use Symfony\Component\Console\Command\Command;
// Importing the input/output interfaces
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HelloCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('hello')
            ->setDescription('Prints Hello')
            ->setHelp('This command prints a simple greeting.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln("\n>>> Hello " . get_current_user() . " <<<\n");

        return Command::SUCCESS;
    }
}

