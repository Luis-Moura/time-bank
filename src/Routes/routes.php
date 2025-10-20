<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Controllers\AuthController;

$app->get('/', function (Request $request, Response $response, array $args) {
  $response->getBody()->write("API TimeBank");

  return $response;
});

$app->post('/register', [$authController, 'register']);
$app->post('/login', [$authController, 'login']);