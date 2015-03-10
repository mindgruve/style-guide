<?php

//CUSTOM PROJECT ROOT PER INSTALL
$PROJECT_ROOT = realpath('..');

require_once $PROJECT_ROOT . '/vendor/autoload.php';

use Mindgruve\StyleGuide;

//GET CONFIG
$config = parse_ini_file($PROJECT_ROOT . '/config/mgStyleGuide.ini');
//RESOLVE RELATIVE MARKUP PATH
if (array_key_exists('markupPath', $config)) {
    if (strpos($config['markupPath'], '/') !== 0) {
        $config['markupPath'] = realpath($PROJECT_ROOT . $config['markupPath']);
    }
}
//SHOW SOURCE CODE COLLAPSING BLOCK
$showSource = !!@$_GET['dev'] || !!@$_GET['source'];

$styleGuide = new StyleGuide($config);
echo $styleGuide->render('index', array('showSource' => $showSource, 'useMinified' => true));
