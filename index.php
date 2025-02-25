<?php
define('DIR_ALMACEN', __DIR__ . "/almacen");

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/libs/autocargador.php';

// Inicializar el manejador de errores
//new ManejadorErrores();
$app = AppFactory::create();

$app->get('/info', function (Request $request, Response $response, $args) {
    ob_start();
    phpinfo();
    $info = ob_get_clean();
    $response->getBody()->write($info);
    return $response;
});

$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write('Esta es la ruta base de la api');

    // Ejemplo de uso
    $tabla = Convenciones::convertirClaseATabla('ColaboradorCargo');
    echo $tabla;  // Salida esperada: 'ColaboradoresCargos'

    $modelo = Convenciones::convertirTablaAClase('ColaboradoresCargos');
    echo $modelo; // Salida esperada: 'ColaboradorCargo'

    $primaryKey = Convenciones::clavePrimaria('ColaboradoresCargos');
    echo $primaryKey; // Salida esperada: 'colaboradorCargoID'

    $campoAuditoria = Convenciones::campoAuditoria('ColaboradoresCargos', 'CREA');
    echo $campoAuditoria; // Salida esperada: 'colaboradorCargoFCHCREA'

    $campoUsuario = Convenciones::campoUsuario('ColaboradoresCargos', 'MODIFICA');
    echo $campoUsuario; // Salida esperada: 'colaboradorCargoUSRMODIFICA'

    $campoEstado = Convenciones::campoEstado('ColaboradoresCargos');
    echo $campoEstado; // Salida esperada: 'colaboradorCargoESTADO'


    // $respuesta = BaseDatos::select(
    //     'sicam_principal.CamaraColaboradores',
    //     '*',
    //     ['CamaraColaboradores.colaboradorID' => [51, 570]]
    // );
    // print_r($respuesta);

    return $response;
});

$app->get('/{componente}/{controlador}/{operacion}', function (Request $request, Response $response, $args) {


    return $response;
});

$app->post('/', function (Request $request, Response $response) {
    $params = $request->getParsedBody();

    // Verificar que los parámetros necesarios estén presentes
    if (!isset($params['componente'], $params['controlador'], $params['operacion'])) {
        $data = ['error' => 'Faltan parámetros requeridos'];
        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $componente = $params['componente'];
    $controlador = $params['controlador'];
    $operacion = $params['operacion'];

    $ConexionBD = ConexionBD::obtenerInstancia('34.66.228.199', 'catorres', 'y<SjEU]YSDusQ#z1');
    $sicam_principal = $ConexionBD->conectar('sicam_principal');
    $respuesta = $sicam_principal->select('CamaraColaboradores', '*', ['colaboradorID' => [51, 570]]);

    // Convertir respuesta en JSON
    $response->getBody()->write(json_encode($respuesta));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});



$app->run();
