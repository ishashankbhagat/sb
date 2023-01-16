<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitf980f56fd837e55063f8a6ddde92ee13
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInitf980f56fd837e55063f8a6ddde92ee13', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitf980f56fd837e55063f8a6ddde92ee13', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitf980f56fd837e55063f8a6ddde92ee13::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
