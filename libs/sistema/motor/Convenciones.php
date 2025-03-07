<?php

class Convenciones {

    // 1. Recibe el nombre de la tabla, por ejemplo "Campanas_Temporadas_Renovaciones"
    public function obtenerPartes($nombre) {
        // Divide el nombre por el guion bajo y devuelve las partes
        //return explode('_', $nombre);
        // 2. Divide el nombre de la clase por la mayúscula
        return preg_split('/(?=[A-Z])/', $nombre);
    }

    // 2. Singuliza las palabras, manejando pluralidades comunes
    public function singulizar($palabra) {
        // Definir un array con los sufijos plurales y sus equivalentes singulares
        $singular = [
            'iones' => 'ion', // "Renovaciones" -> "Renovacion"
            'es' => '', // "Todos" -> "Todo"
            's' => '', // "Campanas" -> "Campana"
                // Puedes agregar más reglas según sea necesario
        ];

        // Recorre el array de sufijos y aplica la regla correspondiente
        foreach ($singular as $plural => $sing) {
            if (substr($palabra, -strlen($plural)) === $plural) {
                return substr($palabra, 0, -strlen($plural)) . $sing;
            }
        }

        // Si no encuentra ningún sufijo, devuelve la palabra original
        return $palabra;
    }

    // 3. Singuliza todas las palabras
    public function singulizarPalabras($partes) {
        return array_map([$this, 'singulizar'], $partes);
    }

    // 4. Convierte la primera palabra a minúscula
    public function convertirPrimeraAminuscula($palabras) {
        // Convierte la primera palabra a minúscula
        $palabras[0] = strtolower($palabras[0]);
        return $palabras;
    }

    // 4. Convierte la primera palabra a mayúscula
    public function convertirPrimeraAmayuscula($palabras) {
        // Convierte la primera palabra a mayúscula
        $palabras[0] = ucfirst($palabras[0]);
        return $palabras;
    }

    // 4. Convierte la primera letra en minuscula
    function primeraLetraMinuscula($cadena) {
        return lcfirst($cadena);
    }

    // 5. Une las palabras para formar un solo nombre
    public function unirPalabras($palabras) {
        return implode('', $palabras);
    }

    // 6. Completa el nombre del campo con la terminación que se le pase
    public function agregarTerminacion($nombreBase, $terminacion) {
        return $nombreBase . $terminacion;
    }

    // 7. Convierte la terminación a mayúsculas
    public function convertirTerminacionMayusculas($nombre) {
        return strtoupper($nombre);
    }

    // 8. Concatenar el prefijo con la terminación
    public function obtenerNombreCampo($nombreTabla, $terminacion) {
        // Obtener las partes del nombre
        $partes = $this->obtenerPartes($nombreTabla);

        // Singulizar las partes
        $singularizadas = $this->singulizarPalabras($partes);

        // Convertir la primera palabra a minúsculas
        $palabrasFinales = $this->convertirPrimeraAmayuscula($singularizadas);

        // Unir las palabras
        $palabrasUnidas = $this->unirPalabras($palabrasFinales);

        //convertir la primera letra en minuscula
        $nombreBase = $this->primeraLetraMinuscula($palabrasUnidas);

        // Convertir la terminación a mayúsculas
        $terminacionMayuscula = $this->convertirTerminacionMayusculas($terminacion);

        // Completar el nombre con la terminación
        $nombreFinal = $this->agregarTerminacion($nombreBase, $terminacionMayuscula);

        return $nombreFinal;
    }

    /**
     * Convierte el nombre de una clase en el nombre de la tabla en plural (PascalCase -> PascalCase en plural)
     */
    public static function convertirClaseATabla($nombreClase) {

        $Conv = new self;

        // Obtener las partes del nombre
        $partes = $Conv->obtenerPartes($nombreClase);

        // Convertir la primera palabra a minúsculas
        $palabrasFinales = $Conv->convertirPrimeraAmayuscula($partes);

        // Unir las palabras
        $nombreBase = $Conv->unirPalabras($palabrasFinales);

        return $nombreBase;
    }

    /**
     * Convierte el nombre de la tabla en el nombre del modelo en singular
     */
    public static function convertirTablaAClase($nombreTabla) {
        $Conv = new self;

        // Obtener las partes del nombre
        $partes = $Conv->obtenerPartes($nombreTabla);

        // Convertir la primera palabra a minúsculas
        $palabrasFinales = $Conv->convertirPrimeraAmayuscula($partes);

        // Unir las palabras
        $nombreBase = $Conv->unirPalabras($palabrasFinales);

        return $nombreBase;
    }

    /**
     * Genera el nombre de la clave primaria
     */
    public static function clavePrimaria($nombreTabla) {
        $Conv = new self;
        return $Conv->obtenerNombreCampo($nombreTabla, 'ID');
    }

    /**
     * Genera el nombre de la clave foránea
     */
    public static function claveForanea($nombreTabla) {
        $Conv = new self;
        return $Conv->obtenerNombreCampo($nombreTabla, 'ID');
    }

    public static function camposAuditoria($nombreTabla, $accion) {
        $Conv = new self;
        return [$Conv->campoFecha($nombreTabla, $accion), $Conv->campoUsuario($nombreTabla, $accion)];
    }

    /**
     * Genera el nombre de un campo de auditoría basado en la acción
     */
    public static function campoFecha($nombreTabla, $accion) {
        $Conv = new self;
        return $Conv->obtenerNombreCampo($nombreTabla, 'FCH' . strtoupper($accion));
    }

    /**
     * Genera el nombre de un campo de usuario basado en la acción
     */
    public static function campoUsuario($nombreTabla, $accion) {
        $Conv = new self;
        return $Conv->obtenerNombreCampo($nombreTabla, 'USR' . strtoupper($accion));
    }

    /**
     * Genera el nombre del campo estado
     */
    public static function campoEstado($nombreTabla) {
        $Conv = new self;
        return $Conv->obtenerNombreCampo($nombreTabla, 'ESTADO');
    }

    /**
     * Pluraliza un nombre en PascalCase (Colaborador -> Colaboradores, Cargo -> Cargos)
     */
    private static function pluralizar($nombre) {
        // Reglas básicas de pluralización para palabras comunes
        $reglas = [
            '/(s|x|z)$/i' => '$1es', // Ejemplo: Cruz -> Cruces
            '/(ch|sh)$/i' => '$1es', // Ejemplo: Archivo -> Archivos
            '/([^aeiou])y$/i' => '$1ies', // Ejemplo: Usuario -> Usuarios
            '/(or)$/i' => '$1es', // Ejemplo: Colaborador -> Colaboradores
            '/$/i' => 's', // Si no coincide con ninguna regla, solo agrega 's'
        ];

        foreach ($reglas as $patron => $reemplazo) {
            if (preg_match($patron, $nombre)) {
                return preg_replace($patron, $reemplazo, $nombre);
            }
        }
        return $nombre . 's'; // Regla por defecto
    }

    /**
     * Singulariza un nombre en PascalCase (Colaboradores -> Colaborador, Cargos -> Cargo)
     */
    private static function singularizar($nombre) {
        // Reglas básicas de singularización
        $reglas = [
            '/(s|x|z)es$/i' => '$1', // Ejemplo: Cruces -> Cruz
            '/(ch|sh)es$/i' => '$1', // Ejemplo: Archivos -> Archivo
            '/([^aeiou])ies$/i' => '$1y', // Ejemplo: Usuarios -> Usuario
            '/(ores)$/i' => 'or', // Ejemplo: Colaboradores -> Colaborador
            '/s$/i' => '', // Quita la 's' final si no coincide con otra regla
        ];

        foreach ($reglas as $patron => $reemplazo) {
            if (preg_match($patron, $nombre)) {
                return preg_replace($patron, $reemplazo, $nombre);
            }
        }
        return rtrim($nombre, 's'); // Regla por defecto
    }

    function snakeToPascal($input) {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $input)));
    }

    function pascalToSnake($input) {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $input));
    }

    function convertirEnArray($nombre) {
        // Divide el nombre por el guion bajo
        $partes = explode('_', $nombre);

        // Convierte cada parte a minúsculas
        return array_map('strtolower', $partes);
    }
}
