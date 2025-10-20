<?php
namespace App\Controllers;

use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

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
}