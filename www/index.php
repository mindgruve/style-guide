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
        $config['markupPath'] = realpath($PROJECT_ROOT . '/' . $config['markupPath']);
    }
}
//SHOW SOURCE CODE COLLAPSING BLOCK
$showSource = !!@$_GET['dev'] || !!@$_GET['source'];

$styleGuide = new StyleGuide($config);

$template = array_key_exists('template', $_GET) ? $_GET['template'] : '';
if ($styleGuide->templateExists($template . '.html')) {
    echo $styleGuide->render('base', array('static_html' => $template . '.html'));
} elseif ($styleGuide->templateExists($template . '.html.twig')) {
    echo $styleGuide->render('base', array('static_html' => $template . '.html.twig'));
} else {
    echo $styleGuide->render('index', array('showSource' => $showSource, 'useMinified' => true));
}
