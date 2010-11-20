<?php

function autoload($className)
{
    list($namespace) = explode('_', $className, 2);
    switch ($namespace) {
        case 'Twig':
        case 'Sprig':
            $path = "$namespace/lib/" . str_replace("_", "/", $className) . ".php";
            require_once dirname(dirname(__FILE__)) . "/$path";
            break;
        default:
            if ($className == 'Smarty' || $className = 'Config_File' || $className = 'Smarty_Compiler') {
                require_once dirname(dirname(__FILE__)) . "/Smarty/libs/$className.class.php";
            }
    }
}

spl_autoload_register('autoload');
