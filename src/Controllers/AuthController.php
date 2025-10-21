<?php
namespace App\Controllers;

use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;
use OpenApi\Attributes as OA;

#[OA\Info(
  version: "1.0.0",
  title: "TimeBank API",
  description: "API para troca de horas entre profissionais - Banco de Tempo"
)]
#[OA\Server(
  url: "http://localhost:8080",
  description: "Servidor de Desenvolvimento"
)]
#[OA\SecurityScheme(
  securityScheme: "bearerAuth",
  type: "http",
  scheme: "bearer",
  bearerFormat: "JWT"
)]
class AuthController
{
  #[OA\Post(
    path: "/register",
    summary: "Registrar novo usuário",
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\JsonContent(
        required: ["name", "email", "password"],
        properties: [
          new OA\Property(property: "name", type: "string", example: "João Silva"),
          new OA\Property(property: "email", type: "string", format: "email", example: "joao@example.com"),
          new OA\Property(property: "password", type: "string", format: "password", example: "senha123")
        ]
      )
    ),
    tags: ["Autenticação"],
    responses: [
      new OA\Response(
        response: 200,
        description: "Usuário criado com sucesso",
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: "message", type: "string", example: "User created"),
            new OA\Property(property: "user_id", type: "integer", example: 1)
          ]
        )
      ),
      new OA\Response(
        response: 400,
        description: "Dados inválidos ou email já existe",
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: "error", type: "string", example: "Email already exists")
          ]
        )
      )
    ]
  )]
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

  #[OA\Post(
    path: "/login",
    summary: "Autenticar usuário",
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\JsonContent(
        required: ["email", "password"],
        properties: [
          new OA\Property(property: "email", type: "string", format: "email", example: "joao@example.com"),
          new OA\Property(property: "password", type: "string", format: "password", example: "senha123")
        ]
      )
    ),
    tags: ["Autenticação"],
    responses: [
      new OA\Response(
        response: 200,
        description: "Login realizado com sucesso",
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: "token", type: "string", example: "eyJ0eXAiOiJKV1QiLCJhbGc...")
          ]
        )
      ),
      new OA\Response(
        response: 401,
        description: "Credenciais inválidas",
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: "error", type: "string", example: "Invalid credentials")
          ]
        )
      )
    ]
  )]
  public function login(Request $request, Response $response)
  {
    $data = $request->getParsedBody();
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    $user = User::where('email', $email)->first();

    if (!$user) {
      $response->getBody()->write(json_encode(['error' => 'Invalid credentials']));

      return $response->withStatus(401)->withHeader('Content-Type', 'application/json');
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