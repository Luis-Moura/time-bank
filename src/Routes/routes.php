<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Middlewares\AuthMiddleware;
use App\Controllers\AuthController;
use App\Controllers\TransactionController;
use App\Controllers\UserController;
use App\Controllers\SkillsController;

$app->get('/', function (Request $request, Response $response, array $args) {
  $response->getBody()->write("API TimeBank");
  return $response;
});

$app->get('/health', function (Request $request, Response $response, array $args) {
  $hostname = gethostname();
  $data = [
    'status' => 'healthy',
    'instance' => $hostname,
    'timestamp' => date('Y-m-d H:i:s')
  ];
  $response->getBody()->write(json_encode($data));
  return $response->withHeader('Content-Type', 'application/json');
});

$app->post('/register', [AuthController::class, 'register']);
$app->post('/login', [AuthController::class, 'login']);

$app->post('/transactions', [TransactionController::class, 'create'])->add(new AuthMiddleware());
$app->get('/transactions', [TransactionController::class, 'getUserTransactions'])->add(new AuthMiddleware());
$app->patch('/transactions/{id}/accept', [TransactionController::class, 'accept'])->add(new AuthMiddleware());
$app->patch('/transactions/{id}/reject', [TransactionController::class, 'reject'])->add(new AuthMiddleware());
$app->get('/transactions/incoming', [TransactionController::class, 'incoming'])->add(new AuthMiddleware());
$app->get('/transactions/available-users', [TransactionController::class, 'getAvailableUsers'])->add(new AuthMiddleware());

$app->post('/skills', [SkillsController::class, 'addSkills'])->add(new AuthMiddleware());
$app->get('/skills', [SkillsController::class, 'listSkills'])->add(new AuthMiddleware());
$app->get('/skills/{id}', [SkillsController::class, 'getSkillById'])->add(new AuthMiddleware());
$app->put('/skills/{id}', [SkillsController::class, 'updateSkill'])->add(new AuthMiddleware());
$app->delete('/skills/{id}', [SkillsController::class, 'deleteSkill'])->add(new AuthMiddleware());

$app->get('/me', [UserController::class, 'me'])->add(new AuthMiddleware());

