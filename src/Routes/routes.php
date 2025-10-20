<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Middlewares\AuthMiddleware;
use App\Controllers\AuthController;

$app->get('/', function (Request $request, Response $response, array $args) {
  $response->getBody()->write("API TimeBank");

  return $response;
});

$app->post('/register', [AuthController::class, 'register']);
$app->post('/login', [AuthController::class, 'login']);

$app->get('/me', function (Request $request, Response $response, array $args) {
  $userId = $request->getAttribute('user_id');

  $user = App\Models\User::find($userId);

  $response->getBody()->write(json_encode($user));

  return $response->withHeader('Content-Type', 'application/json');
})->add(new AuthMiddleware());