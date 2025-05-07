<?php

namespace Fuzzy\Fzkc\Commands;

// Importing the Command base class
use Symfony\Component\Console\Command\Command;
// Importing the input/output interfaces
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class CastleInitCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('castle:init')
            ->setDescription('Init a castle')
            ->setHelp('Init a castle directory')
            ->addArgument('dirname', InputArgument::REQUIRED, 'Project name (Laravels directory name).');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $castleName = $input->getArgument('dirname');
        $castleDirectoryPath = FZKC_CONSOLE_BASE_DIR . DIRECTORY_SEPARATOR . 'laravels' . DIRECTORY_SEPARATOR . $castleName;

        if (!is_dir($castleDirectoryPath)) {
            $output->writeln('Error !!!, castle "' . $castleName . '" not found');
            return Command::FAILURE;
        }
        else {
            $paths = [
                $castleDirectoryPath . DIRECTORY_SEPARATOR . '.env',
                $castleDirectoryPath . DIRECTORY_SEPARATOR . '_docker' . DIRECTORY_SEPARATOR . 'dev' . DIRECTORY_SEPARATOR . '.env',
                $castleDirectoryPath . DIRECTORY_SEPARATOR . '_docker' . DIRECTORY_SEPARATOR . 'dev' . DIRECTORY_SEPARATOR . 'compose.yaml'
            ];

            foreach ($paths as $path) {
                if (is_file($path)) {
                    file_put_contents($path, preg_replace('@{%% CASTLE_NAME %%}@', basename($castleDirectoryPath), file_get_contents($path)));
                }
            }
        }

        $output->writeln('>>> Init castle directory "' . $castleName . '" DONE <<<');

        return Command::SUCCESS;
    }
}

