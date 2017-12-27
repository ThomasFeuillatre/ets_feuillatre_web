<?php
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require_once '../includes/dbOperation.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true
    ]
]);

$app->get('/getPoint',function (Request $request, Response $response)
{
    $db = new DbOperation();
    $points = $db->getListPoint();
    $response->getBody()->write(json_encode(array('points' => $points)));
});



$app->run();
