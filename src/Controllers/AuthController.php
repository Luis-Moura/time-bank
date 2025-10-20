<?php
namespace App\Controllers;

use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;

class AuthController
{
  public function register(Request $request, Response $response, array $args)
  {
    $data = $request->getParsedBody();

    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    if (!$name || !$email || !$password) {
      $response->getBody()->write(json_encode(['error' => 'Missing fields']));

      return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    if (User::where('email', $email)->first()) {
      $response->getBody()->write(json_encode(['error' => 'Email already exists']));

      return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $user = User::create([
      'name' => $name,
      'email' => $email,
      'password' => password_hash($password, PASSWORD_BCRYPT),
    ]);

    $response->getBody()->write(json_encode(['message' => 'User created', 'user_id' => $user->id]));
    return $response->withHeader('Content-Type', 'application/json');
  }

  public function login(Request $request, Response $response)
  {
    $data = $request->getParsedBody();
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    $user = User::where('email', $email)->first();

    if (!$user) {
      $response->getBody()->write(json_encode(['error' => 'User not found']));
    }

    if (!password_verify($password, $user->password)) {
      $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));
      return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
    }

    $payload = [
      'sub' => $user->id,
      'name' => $user->name,
      'email' => $user->email,
      'iat' => time(),
      'exp' => time() + 3600
    ];

    $jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

    $response->getBody()->write(json_encode(['token' => $jwt]));
    return $response->withHeader('Content-Type', 'application/json');
  }
}