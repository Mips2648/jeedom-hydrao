<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitcaa24ca2feccd47e8a6c1bdfc014fa03 {
    private static $loader;

    public static function loadClassLoader($class) {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader() {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInitcaa24ca2feccd47e8a6c1bdfc014fa03', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitcaa24ca2feccd47e8a6c1bdfc014fa03', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitcaa24ca2feccd47e8a6c1bdfc014fa03::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
