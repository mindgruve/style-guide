<?php

require_once '../vendor/autoload.php';

use Mindgruve\StyleGuide;

$config = parse_ini_file('../config.ini');
if (array_key_exists('markupPath', $config)) {
    if (strpos($config['markupPath'], '/') !== 0) {
        $config['markupPath'] = realpath('../' . $config['markupPath']);
    }
}
$styleGuide = new StyleGuide($config);

$showSource = !!@$_GET['dev'] || !!@$_GET['source'];

echo $styleGuide->render('index', array('showSource' => $showSource, 'useMinified' => true));
