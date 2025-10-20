<?php
namespace App\Middlewares;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class AuthMiddleware
{
  public function __invoke(Request $request, $handler): Response
  {
    $authHeader = $request->getHeaderLine('Authorization');
    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
      $response = new \Slim\Psr7\Response();
      $response->getBody()->write(json_encode(['error' => 'Token not provided']));
      return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }

    $token = $matches[1];

    try {
      $decoded = JWT::decode($token, new Key($_ENV['JWT_SECRET'], 'HS256'));

      $request = $request->withAttribute('user_id', $decoded->sub);

      return $handler->handle($request);
    } catch (\Exception $e) {
      $response = new \Slim\Psr7\Response();

      $response->getBody()->write(json_encode(['error' => 'Invalid token']));

      return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }
  }
}
