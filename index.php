<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

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

$app->run();