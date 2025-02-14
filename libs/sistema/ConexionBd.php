<?php

use Medoo\Medoo;

class ConexionBD {
    private static $instancia = null;
    private $conexiones = [];
    private $configuracion = [];

    private function __construct($servidor, $usuario, $contrasena) {
        $this->configuracion = [
            'servidor' => $servidor,
            'usuario' => $usuario,
            'contrasena' => $contrasena
        ];
    }

    public static function obtenerInstancia($servidor, $usuario, $contrasena) {
        if (self::$instancia === null) {
            self::$instancia = new self($servidor, $usuario, $contrasena);
        }
        return self::$instancia;
    }

    public function conectar($nombreBaseDatos) {
        if (!isset($this->conexiones[$nombreBaseDatos])) {
            $this->conexiones[$nombreBaseDatos] = new Medoo([
                'type' => 'mysql',
                'host' => $this->configuracion['servidor'],
                'database' => $nombreBaseDatos,
                'username' => $this->configuracion['usuario'],
                'password' => $this->configuracion['contrasena']
            ]);
        }
        
        return $this->conexiones[$nombreBaseDatos];
    }
}