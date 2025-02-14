<?php

define('DIR_ALMACEN', __DIR__."/almacen");

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/libs/autocargador.php';

// Inicializar el manejador de errores
new ManejadorErrores();
$app = AppFactory::create();

$app->get('/info', function (Request $request, Response $response, $args) {
    ob_start();
    phpinfo();
    $info = ob_get_clean();
    $response->getBody()->write($info);
    return $response;
});

$app->get('/', function(Request $request, Response $response, $args) {
    $response->getBody()->write('Esta es la ruta base de la api');
    return $response;
});

$app->get('/{componente}/{controlador}/{operacion}', function(Request $request, Response $response, $args) {

    print_r($args);

    Bd::table("tabla")->select('*')->where("asdfasdf")->orderBy("afdsf");
    
    return $response;
});

$app->run();