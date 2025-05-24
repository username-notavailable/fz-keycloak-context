<?php

namespace Fuzzy\Cmd\Commands;

use Symfony\Component\Console\Command\Command;
use Dotenv\Dotenv;

class BaseConsoleCmd extends Command
{
    public function makeFilePath(...$pathParts) : string
    {
        return implode(DIRECTORY_SEPARATOR, array_filter(array_filter((array)$pathParts), function($value) { return !empty($value) && is_string($value); }));
    }

    public function makeDirectoryPath(...$pathParts) : string
    {
        return implode(DIRECTORY_SEPARATOR, array_filter(array_filter((array)$pathParts), function($value) { return !empty($value) && is_string($value); })) . DIRECTORY_SEPARATOR;
    }

    public function makeNamespacePath(...$pathParts) : string
    {
        return implode('\\', array_filter(array_filter((array)$pathParts), function($value) { return !empty($value) && is_string($value); }));
    }

    public function setContextEnvVars() : void
    {
        $dotenv = Dotenv::createImmutable($this->makeDirectoryPath(FZKC_CONSOLE_BASE_PATH, 'docker', 'dev'));
        $vars = $dotenv->load();

        foreach ($vars as $key => $value) {
            putenv($key . '=' . $value);
        }
    }

    public function getContextEnvVars() : array
    {
        $dotenv = Dotenv::createMutable($this->makeDirectoryPath(FZKC_CONSOLE_BASE_PATH, 'docker', 'dev'));
        $vars = $dotenv->load();

        $vars['FZKC_PROJECT_NAME'] = basename(FZKC_CONSOLE_BASE_PATH);

        return $vars;
    }

    public function getCastleEnvVars(string $castleName) : array
    {
        $dotenv = Dotenv::createMutable($this->makeDirectoryPath(FZKC_CONSOLE_BASE_PATH, 'laravels', $castleName, '_docker', 'dev'));
        $vars = $dotenv->load();

        $vars['FZKC_CASTLE_NAME'] = $castleName;

        return $vars;
    }
}