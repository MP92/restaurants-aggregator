<?php
spl_autoload_register(function ($className) {
    $paths = [
        "model/",
        "dao/",
    ];
    foreach ($paths as $pathItem) {
        $classFile = ROOT . $pathItem . $className . ".php";
        if (file_exists($classFile)) {
            require $classFile;
        }
    }
});