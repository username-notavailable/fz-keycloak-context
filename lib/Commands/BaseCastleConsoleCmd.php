<?php

namespace Fuzzy\Cmd\Commands;

class BaseCastleConsoleCmd extends BaseConsoleCmd
{
    public function setCastleEnvVars(string $projectName, string $castleName, ?int $castlePort)
    {
        putenv("FZKC_PROJECT_NAME=$projectName");
        putenv("FZKC_CASTLE_NAME=$castleName");

        if (!is_null($castlePort)) {
            putenv("FZKC_CASTLE_PORT=$castlePort");
        }
        //else 
        // "FZKC_CASTLE_PORT" set by docker from castle dev .env 
        //

        $this->setContextEnvVars();
    }
}