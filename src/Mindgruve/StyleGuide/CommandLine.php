<?php

namespace Mindgruve\StyleGuide;

class CommandLine
{
    static $usage = <<<EOF
-------------------------------------------------------------------------------
-------- Mindgruve Style Guide ------------------------------------------------
--
--  Usage
--      generate [path] - generates a style guide. Default action.
--
-------------------------------------------------------------------------------
EOF;

    static public function main($e)
    {
        $io = $e->getIO();
        $args = $e->getArguments();
        $command = @$args[0];
        switch ($command) {
            case 'generate':
                $arg2 = @$args[1];
                if (!$arg2) {
                    $io->writeError('ERROR: You must provide a directory to generate the style guide');
                } else {
                    $generator = new Generator($io);
                    $generator->generate($arg2);
                }
                break;
            default:
                $io->write(self::$usage . PHP_EOL);
                break;
        }
    }
}