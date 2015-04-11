<?php

spl_autoload_register(function ($class) {

    $baseDir = dirname(__FILE__) . '/src';

    require_once $baseDir . '/Qiniu/functions.php';
    $file = $baseDir . DIRECTORY_SEPARATOR . strtr($class, '\\', DIRECTORY_SEPARATOR) . '.php';
    if (file_exists($file)) {
        require $file;
    }
}, true);