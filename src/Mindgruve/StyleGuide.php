<?php
/**
 * Created by PhpStorm.
 * User: Abishai Gray
 * Date: 7/17/14
 * Time: 10:15 PM
 */

namespace Mindgruve;

class StyleGuide
{
    private $configVariables = array();
    static $filenameAndTitles = array();
    private $twigLoader;
    private $twig;

    function __construct($config = null)
    {
        if ($config) {
            if (is_array($config)) {
                $this->configVariables = $config;
                $this->transformConfigVariables();
            } else {
                throw new \InvalidArgumentException('The config you passed is not valid. You must pass an array.');
            }
        } else {
            $this->configVariables = array();
        }

        $this->setupTwig();
    }

    private function setupTwig()
    {
        $paths = array(
            realpath(dirname(dirname(__DIR__)) . '/www/markup'),//deprecated
            realpath(dirname(dirname(__DIR__)) . '/views')
        );

        if (array_key_exists('markupPath', $this->configVariables)) {
            array_unshift($paths, $this->configVariables['markupPath']);
        }

        $this->twigLoader = new \Twig_Loader_Filesystem($paths);
        $this->twig = new \Twig_Environment($this->twigLoader);

        $filenameFilter = new \Twig_SimpleFilter(
            'sgFilename', function ($string) {
            $filenameAndTitle = StyleGuide::getFileNameAndTitle($string);

            return $filenameAndTitle[0];
        }
        );
        $this->twig->addFilter($filenameFilter);

        $titleFilter = new \Twig_SimpleFilter(
            'sgTitle', function ($string) {
            $filenameAndTitle = StyleGuide::getFileNameAndTitle($string);

            return $filenameAndTitle[1];
        }
        );
        $this->twig->addFilter($titleFilter);

        $existsTest = new \Twig_SimpleTest(
            'sgExist', array($this, 'templateExists')
        );
        $this->twig->addTest($existsTest);
    }

    function transformConfigVariables()
    {
        foreach ($this->configVariables as $key => $value) {
            if (is_array($value)) {
                $transformedValue = $value;//Array is set by copy
                foreach ($transformedValue as $arrayKey => $arrayValue) {
                    if (strpos($arrayValue, '[') === 0) {
                        $transformedValue[$arrayKey] = json_decode($arrayValue);
                    }
                }
                $this->configVariables[$key] = $transformedValue;
            } else {
                if (strpos($value, '[') === 0) {
                    $this->configVariables[$key] = json_decode($value);
                }
            }
        }
    }

    function getRedirectUrl($post, $isSecure)
    {
        if (isset($post['sg_uri']) && isset($post['sg_section_switcher'])) {
            // Build out URI to reload from form dropdown
            // Need full url for this to work in Opera Mini
            $pageURL = $isSecure ? "https://" : "http://";
            $pageURL .= $post['sg_uri'] . $post['sg_section_switcher'];

            return $pageURL;
        }

        return '';
    }

    function getConfig()
    {
        return $this->configVariables;
    }

    /**
     * Support loading unique vars from ini
     *
     * @param string $variableName The INI variable to inject
     * @param string $template     String The template to replace
     *
     * @return string
     */
    function formatConfigVariable($variableName, $template = '{0}')
    {
        $config = $this->getConfig();

        $iniVariable = isset($config[$variableName]) ? $config[$variableName] : '';

        if (is_array($iniVariable)) {
            $formatted = array();

            foreach ($iniVariable as $value) {
                $formatted[] = $this->formatVariable($value, $template);
            }

            $result = implode(PHP_EOL, $formatted);
        } else {
            if (strpos($iniVariable, '[') === 0) {
                $iniVariable = json_decode($iniVariable);
            }
            $result = $this->formatVariable($iniVariable, $template);
        }

        return $result;
    }

    /**
     * Basic string injection using {0} and a string or {0}...{n} and an array
     *
     * @param string|array $variable The content to inject
     * @param string       $template The template to replace
     *
     * @return string
     */
    function formatVariable($variable = '', $template = '{0}')
    {
        if (is_array($variable)) {
            for ($i = 0; $i < count($variable); $i++) {
                $template = str_replace('{' . $i . '}', $variable[$i], $template);
            }
        } else {
            $template = str_replace('{0}', $variable, $template);
        }

        return $template;
    }

// Display title of each markup samples as a select option
    function listMarkupAsOptions($type)
    {
        $files = $this->getFilesFromConfigOrMarkupDir($type);
        foreach ($files as $file):
            list($filename, $title) = $this->getFileNameAndTitle($file);
            $title = ucwords($title);
            echo $this->formatVariable(array($filename, $title), '<li><a href="#sg-{0}">{1}</a></li>');
        endforeach;
    }

    /**
     * Display markup view & source
     *
     * @param string $type       The type of content to display. This looks for a folder named "$type" in the markup directory
     * @param bool   $showSource Toggle the display of the "View Source" UX
     */
    function showMarkup($type, $showSource = true)
    {
        $this->showConfigMarkup($type, $showSource);
    }

    /**
     * @param string $variable The name of the config variable that has an array of files to display
     * @param bool   $showSource
     */
    function showConfigMarkup($variable, $showSource = true)
    {
        $files = $this->getFilesFromConfigOrMarkupDir($variable);
        $this->showHtml($files, $showSource);
    }

    /**
     * Display html view & source
     *
     * @param array $files      The files to pull html from
     * @param bool  $showSource Toggle the display of the "View Source" UX
     */
    function showHtml($files, $showSource)
    {
        foreach ($files as $file):
            list($filename, $title) = $this->getFileNameAndTitle($file);
            ?>
            <div class="col-lg-12 sg-section" id="sg-<?php echo $filename; ?>">
                <div class="sg-display">
                    <h2 class="sg-h2"><?php echo $title; ?></h2>
                    <?php include($file); ?>
                </div>
                <!--/.sg-display-->
                <?php if ($showSource) : ?>
                    <?php $code = htmlspecialchars(file_get_contents($file)); ?>
                    <div class="sg-markup-controls">
                        <a class="btn btn-primary sg-btn sg-btn--source" href="#">View Source</a>
                    </div>
                    <div class="sg-source sg-animated">
                        <a class="btn btn-default sg-btn sg-btn--select" href="#">Copy Source</a>
                        <pre
                            class="prettyprint linenums"><code><?php echo $code; ?></code></pre>
                    </div><!--/.sg-source-->
                <?php endif; ?>
            </div><!--/.sg-section-->
        <?php endforeach;
    }

    /**
     * Parse a file path and return the filename without it's extension and the title in an array
     *
     * @param string $file The file path to parse
     *
     * @return array The filename without it's extension is at index 0 & title is at index 1
     */
    static function getFileNameAndTitle($file)
    {
        if (array_key_exists($file, self::$filenameAndTitles) == false) {
            $fileParts = explode('/', $file);
            $filename = array_pop($fileParts);

            $vals = array();
            $vals[] = preg_replace("/\.html$/i", "", $filename);
            $vals[] = preg_replace("/\-/i", " ", $vals[0]);

            self::$filenameAndTitles[$file] = $vals;
        }

        return self::$filenameAndTitles[$file];
    }

    /**
     * Read through a directory and return a list of html files that do NOT begin with an underscore
     *
     * @param string $directory The path within style guide to pull html file names
     *
     * @throws \Exception The directory is not available
     * @return array A list of files
     */
    function getFilesFromDir($directory)
    {
        $files = array();
        $handle = opendir($directory);
        if ($handle) {
            while (false !== ($file = readdir($handle))): //skip over files that begin with underscore and are not html
            {
                if (strpos($file, '_') !== 0 && stristr($file, '.html')):
                    $files[] = $directory . $file;
                endif;
            }
            endwhile;
        } else {
            throw new \Exception("The directory you're trying to read is not available. " . $directory);
        }

        return $files;
    }

    /**
     * Return the list of files in the config with the specified variable name or return a list of all the files in the
     * directory of the same name
     *
     * @param $variableOrDirectory
     *
     * @return array A list of files
     */
    function getFilesFromConfigOrMarkupDir($variableOrDirectory)
    {
        $config = $this->getConfig();
        if (array_key_exists($variableOrDirectory, $config) && count($config[$variableOrDirectory]) > 0) {
            $files = $config[$variableOrDirectory];
        } else {
            $files = $this->getFilesFromDir('markup/' . $variableOrDirectory . '/');
            sort($files);
        }

        return $files;
    }

    function render($templateName, $extraVariables = array())
    {
        if ($this->twig->getLoader()->exists($templateName . '.html.twig')) {
            return $this->twig->render(
                $templateName . '.html.twig',
                array('config' => $this->configVariables, 'extra' => $extraVariables)
            );
        } else {
            return "$templateName does not exist. Looked for $templateName.html.twig.";
        }
    }

    function templateExists($templateName)
    {
        return $this->twigLoader->exists($templateName);
    }
}
