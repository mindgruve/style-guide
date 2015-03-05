<?php
/**
 * Created by PhpStorm.
 * User: agray
 * Date: 3/4/15
 * Time: 11:50 AM
 */

namespace Mindgruve\StyleGuide;

use Composer\Script\Event;

class Generator
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

    private $composer;
	private $io;
	
	function __construct($composer, $io) {
		$this->composer = $composer;
		$this->io = $io;
	}

    static public function commandLine(Event $e)
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
					$generator = new static($composer, $io);
                    $generator->generate($arg2);
                }
                break;
            default:
                $io->write(self::$usage . PHP_EOL);
                break;
        }
    }

    public function generate($targetDirectory)
	{
        $path = $targetDirectory;
        if (strpos($targetDirectory, '/') === 0) {
            $this->io->writeError('Sorry, you must use a relative install path.');
        }
        if (file_exists($path)) {
			$this->io->writeError('Sorry, but ' . $path . ' already exists in ' . realpath('.'));
		} else {
           mkdir($path, 0755, true);
		   $this->io->write('A new directory was made at ' . realpath($path));
        }
    }
}