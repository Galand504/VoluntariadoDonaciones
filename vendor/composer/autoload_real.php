<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInitd64eb552e2c9fc3c2017afc463d5f42e
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

        spl_autoload_register(array('ComposerAutoloaderInitd64eb552e2c9fc3c2017afc463d5f42e', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInitd64eb552e2c9fc3c2017afc463d5f42e', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInitd64eb552e2c9fc3c2017afc463d5f42e::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
