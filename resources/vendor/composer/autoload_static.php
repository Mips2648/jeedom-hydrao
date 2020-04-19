<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit7e879f746082ade8d2b6410cd2ece219
{
    public static $prefixLengthsPsr4 = array (
        'm' => 
        array (
            'mips\\hydraoapi\\' => 15,
        ),
        'P' => 
        array (
            'Psr\\Log\\' => 8,
        ),
        'M' => 
        array (
            'Mips\\Http\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'mips\\hydraoapi\\' => 
        array (
            0 => __DIR__ . '/..' . '/mips/hydrao-api/src',
        ),
        'Psr\\Log\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/log/Psr/Log',
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

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit7e879f746082ade8d2b6410cd2ece219::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit7e879f746082ade8d2b6410cd2ece219::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit7e879f746082ade8d2b6410cd2ece219::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
