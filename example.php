<?php
$vars = array(
    'string' => 'string',
    'array' => array(
        'key_of_a' => 'a', 'key_of_b' => 'b', 'key_of_c' => 'c'
    ),
    'a' => 10, 'b' => 20, 'c' => 30
);

require_once 'bootstrap.php';

$twig = new Sprig_Environment(
    new Twig_Loader_Filesystem('./'),
    array(
        'cache' => dirname(__FILE__) . '/tmp/',
        'debug' => true
    )
);
$twig->enableDebug(true);
$twig->addExtension(new Sprig_Extension_Smarty());

try {
    $template = $twig->loadTemplate('example.smarty');
    $template->display($vars);
} catch(Exception $e) {
    echo $e;
}



$twig = new Twig_Environment(new Twig_Loader_Filesystem('.'), array('cache' => dirname(__FILE__) . '/tmp/', 'debug' => true));
$template = $twig->loadTemplate('example.twig');
$template->display($vars);
