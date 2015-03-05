<?php

namespace Mindgruve\StyleGuide;

use Composer\Script\Event;

class CommandLine
{
    static $usage = '\
-----------------------------------------------------------\
-------- Mindgruve Style Guide ----------------------------\
--                                                         \
--  Usage                                                  \
--      generate - generates a style guide. Default action.\
--                                                         \
-----------------------------------------------------------\
';

    static public function main(Event $e)
    {
        $composer = $e->getComposer();
        $io = $e->getIO();

        $args = $e->getArguments();
        $command = @$args[0];
        switch ($command) {
            case 'generate':
                $arg2 = @$args[1];
                if (!$arg2) {
                    $io->writeError('ERROR: You must provide a directory to generate the style guide');
                } else {
                    $generator = new Generator($composer, $io);
                    $generator->generate($arg2);
                }
                break;
            default:
                $io->write(self::$usage . PHP_EOL);
                break;
        }
    }
}