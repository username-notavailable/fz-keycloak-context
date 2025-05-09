<?php

namespace Fuzzy\Fzkc\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;


class ReplaceProjectNameCommand extends BaseCastleConsoleCmd
{
    protected function configure()
    {
        $this
            ->setName('replace:project:name')
            ->setDescription('Replace project name')
            ->setHelp('Replace "{%% FZKC_PROJECT_NAME %%}" with the project name into a file')
            ->addArgument('dirname', InputArgument::REQUIRED, 'Fzkc castle name (laravels subdirectory name).')
            ->addArgument('path', InputArgument::REQUIRED, 'Target file path relative to the castle directory.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $castleName = $input->getArgument('dirname');
        $castleDirectoryPath = $this->makeDirectoryPath(FZKC_CONSOLE_BASE_PATH, 'laravels', $castleName);
        $projectName = basename(FZKC_CONSOLE_BASE_PATH);

        if (!is_dir($castleDirectoryPath)) {
            if (!$input->getOption('quiet')) {
                $output->writeln('!!! Fzkc castle directory "' . $castleName . '" not exists !!!');
            }

            return Command::FAILURE;
        }
        else {
            $targetPath = $this->makeFilePath(rtrim($castleDirectoryPath, '/'), $input->getArgument('path'));

            if (is_file($targetPath)) {
                file_put_contents($targetPath, preg_replace('@{%% FZKC_PROJECT_NAME %%}@', $projectName, file_get_contents($targetPath)));

                if (!$input->getOption('quiet')) {
                    $output->writeln(">>> \"{%% FZKC_PROJECT_NAME %%}\" replaced with \"$projectName\" into \"$targetPath\" <<<");
                }
        
                return Command::SUCCESS;
            }
            else {
                if (!$input->getOption('quiet')) {
                    $output->writeln('!!! File "' . $targetPath . '" not exists !!!');
                }

                return Command::FAILURE;
            }
        }
    }
}

