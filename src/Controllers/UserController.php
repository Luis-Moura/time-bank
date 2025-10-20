<?php

namespace App\Controllers;

use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Attributes as OA;

class UserController
{
  #[OA\Get(
    path: "/me",
    summary: "Obter informações do usuário autenticado",
    security: [["bearerAuth" => []]],
    tags: ["Usuário"],
    responses: [
      new OA\Response(
        response: 200,
        description: "Informações do usuário",
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: "id", type: "integer", example: 1),
            new OA\Property(property: "name", type: "string", example: "João Silva"),
            new OA\Property(property: "email", type: "string", example: "joao@example.com"),
            new OA\Property(property: "created_at", type: "string", format: "date-time"),
            new OA\Property(property: "updated_at", type: "string", format: "date-time")
          ]
        )
      ),
      new OA\Response(response: 401, description: "Não autenticado")
    ]
  )]
  public function me(Request $request, Response $response)
  {
    $userId = $request->getAttribute('user_id');
    $user = User::find($userId);

    $response->getBody()->write(json_encode($user));

    return $response->withHeader('Content-Type', 'application/json');
  }
}
