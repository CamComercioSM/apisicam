<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
define('DIR_SICAM', __DIR__);
define('DIR_ALMACEN', DIR_SICAM . "/almacen");
define('DIR_LIBS', DIR_SICAM . "/libs");

require_once  __DIR__ . '/vendor/autoload.php';
require_once  DIR_LIBS . '/autocargador.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;

AppFactory::setResponseFactory(new ResponseFactory());

$app = AppFactory::create();

// Definir una ruta de prueba
$app->get('/', function (Request $request, Response $response, array $args) {

    
    // Ejemplo de uso
    $tabla = Convenciones::convertirClaseATabla('ColaboradorCargo');
    echo $tabla;  // Salida esperada: 'Colaboradores_Cargos'
    echo "<br />";

    $modelo = Convenciones::convertirTablaAClase('ColaboradoresCargos');
    echo $modelo; // Salida esperada: 'ColaboradorCargo'
    echo "<br />";

    $primaryKey = Convenciones::clavePrimaria('ColaboradoresCargos');
    echo $primaryKey; // Salida esperada: 'colaboradorCargoID'
    echo "<br />";

    $campoAuditoria = Convenciones::campoAuditoria('ColaboradoresCargos', 'CREA');
    echo $campoAuditoria; // Salida esperada: 'colaboradorCargo_FCHCREA'
    echo "<br />";

    $campoUsuario = Convenciones::campoUsuario('ColaboradoresCargos', 'MODIFICA');
    echo $campoUsuario; // Salida esperada: 'colaboradorCargoUSRMODIFICA'
    echo "<br />";

    $campoEstado = Convenciones::campoEstado('ColaboradoresCargos');
    echo $campoEstado; // Salida esperada: 'colaboradorCargoESTADO'
    echo "<br />";

    $Colaborador = new Colaborador([ [$moduloAtencionID, $personaID, $colaboradorID, $citaFCHCITA, $citaESTADO], [  ] ]);

    $response->getBody()->write("¡Slim Framework funcionando correctamente!");
    return $response;
});

$app->run();
