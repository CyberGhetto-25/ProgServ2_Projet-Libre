<?php


spl_autoload_register(function (string $className) {

    $baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR;


    $filePath = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $className) . '.php';

    
    if (file_exists($filePath)) {
        require_once $filePath;
        return true; 
    }

    return false; 
});