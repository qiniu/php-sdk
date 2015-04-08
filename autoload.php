<?php

class AutoloaderInit
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('AutoloaderInit', 'loadClassLoader'), true, true);
        self::$loader = $loader = new ClassLoader();
        spl_autoload_unregister(array('AutoloaderInit', 'loadClassLoader'));

        $baseDir = dirname(__FILE__);

        $map = array(
            'Qiniu\\' => array($baseDir . '/src/Qiniu'),
        );
        foreach ($map as $namespace => $path) {
            $loader->setPsr4($namespace, $path);
        }
        $loader->register(true);

        $includeFiles = array(
            $baseDir . '/src/Qiniu/functions.php',
        );
        foreach ($includeFiles as $file) {
            require $file;
        }

        return $loader;
    }
}

return AutoloaderInit::getLoader();