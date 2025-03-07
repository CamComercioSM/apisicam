<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define('DIR_SICAM', __DIR__);
define('DIR_ALMACEN', DIR_SICAM . "/almacen");
define('DIR_LIBS', DIR_SICAM . "/libs");

require_once __DIR__ . '/vendor/autoload.php';
require_once DIR_LIBS . '/autocargador.php';

use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\ResponseFactory;

AppFactory::setResponseFactory(new ResponseFactory());
$app = AppFactory::create();

// Middleware para forzar JSON en las respuestas
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(true, true, true);

// 🔐 Middleware de autenticación básica
$authMiddleware = function (Request $request, RequestHandler $handler) {
    $usuariosValidos = [
        'SKuip064CBoyqfEZV1LtQf0SQf9L2h6aijY15+x5HIvLD6qSODQSsJdPq5EQzkQd' => '6AfpZApDsYmXwQEdopwlV3G5ZQ3fd4rX8ymAPmXHiic=',
        'usuario' => 'claveSegura'
    ];

    $headers = $request->getServerParams();
    
    if (!isset($headers['PHP_AUTH_USER'], $headers['PHP_AUTH_PW'])) {
        $response = new Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["error" => "Acceso no autorizado"], JSON_UNESCAPED_UNICODE));
        return $response->withStatus(401)
                        ->withHeader('WWW-Authenticate', 'Basic realm="SICAM API"')
                        ->withHeader('Content-Type', 'application/json');
    }

    $user = $headers['PHP_AUTH_USER'];
    $pass = $headers['PHP_AUTH_PW'];

    if (!isset($usuariosValidos[$user]) || $usuariosValidos[$user] !== $pass) {
        $response = new Slim\Psr7\Response();
        $response->getBody()->write(json_encode(["error" => "Credenciales incorrectas"], JSON_UNESCAPED_UNICODE));
        return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
    }

    return $handler->handle($request);
};

// 📌 Ruta protegida con Auth Basic
$app->get('/privado', function (Request $request, Response $response) {
    $data = ["message" => "Acceso autorizado a la zona privada"];
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
})->add($authMiddleware);

// 📌 Ruta pública sin autenticación
$app->get('/publico', function (Request $request, Response $response) {
    $data = ["message" => "Esta ruta es pública"];
    $response->getBody()->write(json_encode($data, JSON_UNESCAPED_UNICODE));
    return $response->withHeader('Content-Type', 'application/json');
});

// Ruta para manejar peticiones dinámicas y responder en JSON
$app->get('/{componente}/{controlador}/{operacion}[/{params:.*}]', function (Request $request, Response $response, array $args) {
    $data = [
        "status" => "success",
        "message" => "GET Solicitud procesada correctamente",
        "params" => $args,
        "peticion" => $request,
        "respuesta" => $response
    ];

    // Convertir el array a JSON
    $payload = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    // Escribir la respuesta con encabezado JSON
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

// Ruta para manejar peticiones dinámicas y responder en JSON
$app->post('/{componente}/{controlador}/{operacion}[/{params:.*}]', function (Request $request, Response $response, array $args) {
    $data = [
        "status" => "success",
        "message" => "POST Solicitud procesada correctamente",
        "params" => $args,
        "peticion" => $request,
        "respuesta" => $response
    ];

    // Convertir el array a JSON
    $payload = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    // Escribir la respuesta con encabezado JSON
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
})->add($authMiddleware);

// Ruta de prueba en JSON
$app->get('/', function (Request $request, Response $response, array $args) {
    $data = [
        "message" => "Slim Framework funcionando correctamente",
        "ejemplo" => [
            "tabla" => "ColaboradoresCargos",
            "modelo" => Convenciones::convertirTablaAClase("ColaboradoresCargos"),
            "primaryKey" => Convenciones::clavePrimaria("ColaboradoresCargos"),
            "estado" => Convenciones::campoEstado("ColaboradoresCargos")
        ]
    ];

    // Convertir el array a JSON
    $payload = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    // Escribir la respuesta con encabezado JSON
    $response->getBody()->write($payload);
    return $response->withHeader('Content-Type', 'application/json');
});

$app->run();
