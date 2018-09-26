<?php

// spl_autoload_register(function($class) {
//     if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . "..". DIRECTORY_SEPARATOR ."functions" . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR ."{$class}.php")):
//         require(__DIR__ . DIRECTORY_SEPARATOR . "..". DIRECTORY_SEPARATOR ."functions" . DIRECTORY_SEPARATOR .  "app" . DIRECTORY_SEPARATOR . "{$class}.php");
//     endif;
// });

spl_autoload_register(function ($class) {

    // project-specific namespace prefix
    $prefix = 'Cpts\\';

    // base directory for the namespace prefix
    $base_dir = __DIR__ . DIRECTORY_SEPARATOR . "..". DIRECTORY_SEPARATOR ."functions" . DIRECTORY_SEPARATOR . "cpts". DIRECTORY_SEPARATOR;

    // does the class use the namespace prefix?
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        // no, move to the next registered autoloader
        return;
    }

    // get the relative class name
    $relative_class = substr($class, $len);

    // replace the namespace prefix with the base directory, replace namespace
    // separators with directory separators in the relative class name, append
    // with .php
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    // if the file exists, require it
    if (file_exists($file)) {
        require $file;
    }
});
