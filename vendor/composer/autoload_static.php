<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbf5db67159ce32c75a265a9e4e23b72c
{
    public static $prefixLengthsPsr4 = array (
        'w' => 
        array (
            'wfm\\' => 4,
        ),
        'a' => 
        array (
            'app\\' => 4,
        ),
        'V' => 
        array (
            'Valitron\\' => 9,
        ),
        'R' => 
        array (
            'RedBeanPHP\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'wfm\\' => 
        array (
            0 => __DIR__ . '/..' . '/wfm',
        ),
        'app\\' => 
        array (
            0 => __DIR__ . '/../..' . '/app',
        ),
        'Valitron\\' => 
        array (
            0 => __DIR__ . '/..' . '/vlucas/valitron/src/Valitron',
        ),
        'RedBeanPHP\\' => 
        array (
            0 => __DIR__ . '/..' . '/gabordemooij/redbean/RedBeanPHP',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbf5db67159ce32c75a265a9e4e23b72c::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbf5db67159ce32c75a265a9e4e23b72c::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitbf5db67159ce32c75a265a9e4e23b72c::$classMap;

        }, null, ClassLoader::class);
    }
}
