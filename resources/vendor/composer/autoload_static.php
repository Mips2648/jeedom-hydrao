<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4c5505aa01f6e6089694deaedacf2f8b
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

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4c5505aa01f6e6089694deaedacf2f8b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4c5505aa01f6e6089694deaedacf2f8b::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit4c5505aa01f6e6089694deaedacf2f8b::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
