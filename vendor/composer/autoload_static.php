<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit76fe3a8bb857f102bed1e174f7310267
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'M' => 
        array (
            'Mips\\HydraoClient\\' => 18,
            'Mips\\Http\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
        ),
        'Mips\\HydraoClient\\' => 
        array (
            0 => __DIR__ . '/..' . '/mips/hydrao-api/src',
        ),
        'Mips\\Http\\' => 
        array (
            0 => __DIR__ . '/..' . '/mips/httpclient/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'M' => 
        array (
            'MipsEqLogicTrait' => 
            array (
                0 => __DIR__ . '/..' . '/mips/jeedom-tools/src',
            ),
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit76fe3a8bb857f102bed1e174f7310267::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit76fe3a8bb857f102bed1e174f7310267::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit76fe3a8bb857f102bed1e174f7310267::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit76fe3a8bb857f102bed1e174f7310267::$classMap;

        }, null, ClassLoader::class);
    }
}