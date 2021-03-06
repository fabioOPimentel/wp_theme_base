<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4bdcea73c0c5c179bc6ffb6fa36acfda
{
    public static $prefixLengthsPsr4 = array (
        'W' => 
        array (
            'Walkers\\' => 8,
        ),
        'S' => 
        array (
            'StoutLogic\\AcfBuilder\\' => 22,
        ),
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
        'L' => 
        array (
            'Library\\' => 8,
        ),
        'H' => 
        array (
            'Helpers\\' => 8,
        ),
        'F' => 
        array (
            'Filters\\' => 8,
        ),
        'D' => 
        array (
            'Doctrine\\Instantiator\\' => 22,
            'Doctrine\\Common\\Inflector\\' => 26,
        ),
        'C' => 
        array (
            'Cpts\\' => 5,
        ),
        'A' => 
        array (
            'Actions\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Walkers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/walkers',
        ),
        'StoutLogic\\AcfBuilder\\' => 
        array (
            0 => __DIR__ . '/..' . '/stoutlogic/acf-builder/src',
        ),
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
        'Library\\' => 
        array (
            0 => __DIR__ . '/../..' . '/functions/library',
        ),
        'Helpers\\' => 
        array (
            0 => __DIR__ . '/../..' . '/functions/helpers',
        ),
        'Filters\\' => 
        array (
            0 => __DIR__ . '/../..' . '/functions/filters',
        ),
        'Doctrine\\Instantiator\\' => 
        array (
            0 => __DIR__ . '/..' . '/doctrine/instantiator/src/Doctrine/Instantiator',
        ),
        'Doctrine\\Common\\Inflector\\' => 
        array (
            0 => __DIR__ . '/..' . '/doctrine/inflector/lib/Doctrine/Common/Inflector',
        ),
        'Cpts\\' => 
        array (
            0 => __DIR__ . '/../..' . '/functions/cpts',
        ),
        'Actions\\' => 
        array (
            0 => __DIR__ . '/../..' . '/functions/actions',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4bdcea73c0c5c179bc6ffb6fa36acfda::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4bdcea73c0c5c179bc6ffb6fa36acfda::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
