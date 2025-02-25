<?php

/**
 * Autoloader personalizado para cargar clases desde las carpetas definidas.
 */
spl_autoload_register(function ($nombreClase) {
    $carpetas = [
        DIR_LIBS . '/sistema/',
    ];

    $nombreClase = str_replace('\\', '/', $nombreClase) . '';    
    $rutasExploradas = [];
    foreach ($carpetas as $directorioBase) {
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($directorioBase, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $archivo) {
            $rutaActual = $archivo->getPathname();
            $rutasExploradas[] = $rutaActual;
            if (basename($rutaActual, '.php') === $nombreClase) {                
                include_once $rutaActual;
                return;
            }            
        }
    }
});
