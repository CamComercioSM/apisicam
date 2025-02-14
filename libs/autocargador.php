<?php
require_once  __DIR__.'/../vendor/autoload.php';

/**
 * Autoloader personalizado para cargar clases desde las carpetas definidas.
 */
spl_autoload_register(function ($nombreClase) {
    $directorio = __DIR__ . '/sistema/'; // Ruta donde están las clases
    $archivo = $directorio . $nombreClase . '.php';

    if (file_exists($archivo)) {        
        require_once $archivo;
    }


});