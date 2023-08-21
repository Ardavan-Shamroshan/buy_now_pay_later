<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit5eca7f983cbc4003161026bf891a3dad
{
    public static $files = array (
        '82cef5b12725e983565d844bd37ccaa3' => __DIR__ . '/../..' . '/include/Helpers/helpers.php',
    );

    public static $prefixLengthsPsr4 = array (
        'I' => 
        array (
            'Inc\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Inc\\' => 
        array (
            0 => __DIR__ . '/../..' . '/include',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit5eca7f983cbc4003161026bf891a3dad::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit5eca7f983cbc4003161026bf891a3dad::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit5eca7f983cbc4003161026bf891a3dad::$classMap;

        }, null, ClassLoader::class);
    }
}
