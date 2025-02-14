<?php

class ManejadorErrores
{
    protected string $vistaError = __DIR__ . '/vistas/error.php';
    
    public function __construct()
    {
        set_error_handler([$this, 'manejarError']);
        set_exception_handler([$this, 'manejarExcepcion']);
        register_shutdown_function([$this, 'manejarCierre']);
    }

    public function manejarError(int $codigoError, string $mensajeError, string $archivoError, int $lineaError): void
    {
        $this->registrarError("Error [$codigoError]: $mensajeError en $archivoError línea $lineaError");
        $this->mostrarPaginaError(500, 'Ocurrió un error en el sistema.');
    }

    public function manejarExcepcion(Throwable $excepcion): void
    {
        $this->registrarError("Excepción: " . $excepcion->getMessage() . " en " . $excepcion->getFile() . " línea " . $excepcion->getLine());
        $this->mostrarPaginaError(500, 'Ocurrió una excepción en el sistema.');
    }

    public function manejarCierre(): void
    {
        $error = error_get_last();
        if ($error !== null) {
            $this->registrarError("Error Fatal: {$error['message']} en {$error['file']} línea {$error['line']}");
            $this->mostrarPaginaError(500, 'Ocurrió un error fatal en el sistema.');
        }
    }

    protected function registrarError(string $mensaje): void
    {
        error_log("[" . date('Y-m-d H:i:s') . "] " . $mensaje . "\n", 3, DIR_ALMACEN . '/registros/error.log');
    }

    protected function mostrarPaginaError(int $codigoEstado, string $mensaje): void
    {
        http_response_code($codigoEstado);
        if (file_exists($this->vistaError)) {
            include $this->vistaError;
        } else {
            echo "<h1>Error $codigoEstado</h1><p>$mensaje</p>";
        }
        exit;
    }
}
