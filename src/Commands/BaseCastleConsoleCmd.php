<?php

namespace Fuzzy\Fzkc\Commands;

use Dotenv\Dotenv;


class BaseCastleConsoleCmd extends BaseConsoleCmd
{
    public function setCastleEnvVars(string $projectName, string $castleName, ?int $castlePort)
    {
        putenv("FZKC_PROJECT_NAME=$projectName");
        putenv("FZKC_CASTLE_NAME=$castleName");

        if (!is_null($castlePort)) {
            putenv("FZKC_CASTLE_PORT=$castlePort");
        }

        // "FZKC_CASTLE_PORT" set by .env 

        $dotenv = Dotenv::createImmutable($this->makeDirectoryPath(FZKC_CONSOLE_BASE_PATH, 'docker', 'dev'));
        $vars = $dotenv->load();

        foreach ($vars as $key => $value) {
            putenv($key . '=' . $value);
        }
    }
}