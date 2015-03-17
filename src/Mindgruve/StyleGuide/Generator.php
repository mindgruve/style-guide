<?php
/**
 * Created by PhpStorm.
 * User: agray
 * Date: 3/4/15
 * Time: 11:50 AM
 */

namespace Mindgruve\StyleGuide;

use Symfony\Component\Filesystem\Filesystem;

class Generator
{
    private $io;
    private $projectRoot;

    function __construct($io)
    {
        $this->io = $io;
        $this->filesystem = new Filesystem();
        $this->projectRoot = realpath('.');
    }

    public function generate($targetDirectory)
    {
        $this->generateConfigfile();
        $this->generateMiniSite($targetDirectory);
    }

    public function generateConfigFile()
    {
        $source = realpath(__DIR__ . '/../../../config/mgStyleGuide.default.ini');
        $dest = $this->projectRoot . '/config/mgStyleGuide.ini';//Mindgruve standard config location

        if ($this->filesystem->exists($dest)) {
            $this->io->writeError('Sorry, but ' . realpath($dest) . ' already exists.');
            return;
        }

        $this->filesystem->copy($source, $dest);
        $this->io->write('A new config file was made at ' . realpath($dest) . ' with the default configuration to get you started.');
    }

    public function generateMiniSite($targetDirectory)
    {
        $path = $targetDirectory;

        if (strpos($targetDirectory, '/') === 0) {
            $this->io->writeError('Sorry, you must use a relative install path.');
            return;
        }
        if ($this->filesystem->exists($path)) {
            $this->io->writeError('Sorry, but ' . $path . ' already exists in ' . realpath('.'));
            return;
        }

        $this->filesystem->mkdir($path, 0755);
        $this->filesystem->copy(__DIR__ . '/../../../www/.htaccess', $path . '/.htaccess');
        $this->filesystem->mirror(__DIR__ . '/../../../www/bin', $path . '/bin');
        $this->filesystem->mkdir($path . '/markup');
        $indexFileContents = $this->getFileContents(__DIR__ . '/../../../www/index.php');
        $indexFileContents = preg_replace('/\$PROJECT_ROOT = .*;/', '$PROJECT_ROOT = \'' . $this->projectRoot . '\';', $indexFileContents);
        $this->filesystem->dumpFile($path . '/index.php', $indexFileContents);
        $this->io->write('A new directory was made at ' . realpath($path) . ' with the style guide site files.');
    }

    public function getFileContents($path)
    {
        $realpath = realpath($path);
        $file = fopen($realpath, 'r') or die('Unable to open file at location ' . $realpath . '!');
        $contents = fread($file, filesize($realpath));
        fclose($file);
        return $contents;
    }

    public function findProjectRoot($path)
    {
        $searchPath = realpath($path);
        while (true) {
            if ($this->filesystem->exists($searchPath . '/composer.json')) {
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
