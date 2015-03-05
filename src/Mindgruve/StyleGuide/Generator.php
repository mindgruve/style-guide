<?php
/**
 * Created by PhpStorm.
 * User: agray
 * Date: 3/4/15
 * Time: 11:50 AM
 */

namespace Mindgruve\StyleGuide;

use Composer\Script\Event;
use Symfony\Component\Filesystem\Filesystem;

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
        $filesystem = new Filesystem();
        $path = $targetDirectory;
        
        if (strpos($targetDirectory, '/') === 0) {
            $this->io->writeError('Sorry, you must use a relative install path.');
            return;
        }
        if ($filesystem->exists($path)) {
			$this->io->writeError('Sorry, but ' . $path . ' already exists in ' . realpath('.'));
            return;
		}
                
        $filesystem->mkdir($path, 0755);
        $filesystem->mirror(__DIR__ . '/../../../www/bin', $path . '/bin');
        $filesystem->mkdir($path . '/markup');
        $projectRoot = $this->findProjectRoot($path, $filesystem);
        
        if ($projectRoot === false) {
            $this->writeError('Could not find the project root.');
            return;
        }

        $indexFileContents = $this->getFileContents(__DIR__ . '/../../../www/index.php');
        $indexFileContents = preg_replace('/\$PROJECT_ROOT = .*;/', '$PROJECT_ROOT = \'' . $projectRoot . '\');', $indexFileContents);
        $filesystem->dumpFile($path . '/index.php', $indexFileContents);
        $this->io->write('A new directory was made at ' . realpath($path) . ' with the style guide site files.');
    }
    
    public function getFileContents($path)
    {
        $realpath = realpath($path);
        $file = fopen($realpath, 'r') or die('Unable to open file!');
        $contents = fread($file,filesize($realpath));
        fclose($file);
        return $contents;
    }
    
    public function findProjectRoot($path, Filesystem $filesystem)
    {
        $searchPath = realpath($path);
        while (true) {
            if ($filesystem->exists($searchPath . '/composer.json')) {
                break;
            }
            if ($searchPath == '/') {
                return false;
            }
            $this->io->write("Could not find project root at $searchPath");
            $searchPath = dirname($searchPath);
        }
        return $searchPath;
    }
}
