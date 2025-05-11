<?php

namespace Fuzzy\Cmd\Commands;

use Symfony\Component\Console\Command\Command;


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
}