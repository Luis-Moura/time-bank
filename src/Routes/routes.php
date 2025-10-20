<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Middlewares\AuthMiddleware;
use App\Controllers\AuthController;
use App\Controllers\TransactionController;

$app->get('/', function (Request $request, Response $response, array $args) {
  $response->getBody()->write("API TimeBank");

  return $response;
});

$app->post('/register', [AuthController::class, 'register']);
$app->post('/login', [AuthController::class, 'login']);

$app->post('/transactions', [TransactionController::class, 'create'])->add(new AuthMiddleware());
$app->get('/transactions', [TransactionController::class, 'getUserTransactions'])->add(new AuthMiddleware());
$app->patch('/transactions/{id}/accept', [TransactionController::class, 'accept'])->add(new AuthMiddleware());
$app->patch('/transactions/{id}/reject', [TransactionController::class, 'reject'])->add(new AuthMiddleware());
$app->get('/transactions/incoming', [TransactionController::class, 'incoming'])->add(new AuthMiddleware());
$app->get('/transactions/available-users', [TransactionController::class, 'getAvailableUsers'])->add(new AuthMiddleware());

$app->get('/me', function (Request $request, Response $response, array $args) {
  $userId = $request->getAttribute('user_id');

  $user = App\Models\User::find($userId);

  $response->getBody()->write(json_encode($user));

  return $response->withHeader('Content-Type', 'application/json');
})->add(new AuthMiddleware());