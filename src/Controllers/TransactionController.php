<?php

namespace App\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Attributes as OA;

class TransactionController
{
  #[OA\Post(
    path: "/transactions",
    summary: "Criar nova transação de horas",
    security: [["bearerAuth" => []]],
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\JsonContent(
        required: ["toUserId", "hours", "description"],
        properties: [
          new OA\Property(property: "toUserId", type: "integer", example: 2),
          new OA\Property(property: "hours", type: "number", format: "float", example: 2.5),
          new OA\Property(property: "description", type: "string", example: "Ajuda com desenvolvimento web")
        ]
      )
    ),
    tags: ["Transações"],
    responses: [
      new OA\Response(
        response: 200,
        description: "Transação criada com sucesso",
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: "id", type: "integer", example: 1),
            new OA\Property(property: "from_user_id", type: "integer", example: 1),
            new OA\Property(property: "to_user_id", type: "integer", example: 2),
            new OA\Property(property: "hours", type: "number", example: 2.5),
            new OA\Property(property: "description", type: "string", example: "Ajuda com desenvolvimento web"),
            new OA\Property(property: "status", type: "string", example: "pending"),
            new OA\Property(property: "created_at", type: "string", format: "date-time"),
            new OA\Property(property: "updated_at", type: "string", format: "date-time")
          ]
        )
      ),
      new OA\Response(response: 400, description: "Dados inválidos"),
      new OA\Response(response: 401, description: "Não autenticado")
    ]
  )]
  public function create(Request $request, Response $response)
  {
    $userId = $request->getAttribute('user_id');
    $data = $request->getParsedBody();
    $toUserId = $data['toUserId'] ?? '';
    $hours = $data['hours'] ?? '';
    $description = $data['description'] ?? '';

    if ($toUserId === $userId) {
      $response->getBody()->write(json_encode(["error" => "You cannot send hours to yourself."]));

      return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    if (!$toUserId || !$hours || !$description) {
      $response->getBody()->write(json_encode(['error' => 'Missing fields']));

      return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $transaction = Transaction::create([
      'from_user_id' => $userId,
      'to_user_id' => $toUserId,
      'hours' => $hours,
      'description' => $description
    ]);

    $response->getBody()->write(json_encode($transaction));
    return $response->withHeader('Content-Type', 'application/json');
  }

  #[OA\Get(
    path: "/transactions",
    summary: "Listar todas as transações do usuário",
    security: [["bearerAuth" => []]],
    tags: ["Transações"],
    responses: [
      new OA\Response(
        response: 200,
        description: "Lista de transações",
        content: new OA\JsonContent(
          type: "array",
          items: new OA\Items(
            properties: [
              new OA\Property(property: "id", type: "integer"),
              new OA\Property(property: "from_user_id", type: "integer"),
              new OA\Property(property: "to_user_id", type: "integer"),
              new OA\Property(property: "hours", type: "number"),
              new OA\Property(property: "description", type: "string"),
              new OA\Property(property: "status", type: "string", enum: ["pending", "accepted", "rejected"]),
              new OA\Property(property: "created_at", type: "string", format: "date-time"),
              new OA\Property(property: "updated_at", type: "string", format: "date-time")
            ]
          )
        )
      ),
      new OA\Response(response: 401, description: "Não autenticado")
    ]
  )]
  public function getUserTransactions(Request $request, Response $response)
  {
    $userId = $request->getAttribute('user_id');

    $transactions = Transaction::where('from_user_id', $userId)
      ->orWhere('to_user_id', $userId)
      ->get();

    $response->getBody()->write(json_encode($transactions));
    return $response->withHeader('Content-Type', 'application/json');
  }

  #[OA\Patch(
    path: "/transactions/{id}/accept",
    summary: "Aceitar uma transação pendente",
    security: [["bearerAuth" => []]],
    tags: ["Transações"],
    parameters: [
      new OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer"),
        example: 1
      )
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: "Transação aceita com sucesso",
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: "id", type: "integer"),
            new OA\Property(property: "status", type: "string", example: "accepted")
          ]
        )
      ),
      new OA\Response(response: 400, description: "Transação já processada"),
      new OA\Response(response: 404, description: "Transação não encontrada"),
      new OA\Response(response: 401, description: "Não autenticado")
    ]
  )]
  public function accept(Request $request, Response $response, array $args)
  {
    $transactionId = $args['id'];
    $userId = $request->getAttribute('user_id');

    $transaction = Transaction::find($transactionId);

    if (!$transaction || $transaction->to_user_id != $userId) {
      $response->getBody()->write(json_encode(['error' => 'Transaction not found']));

      return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    if ($transaction->status !== 'pending') {
      $response->getBody()->write(json_encode(['error' => 'Transaction already processed']));

      return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $transaction->status = 'accepted';
    $transaction->save();

    $response->getBody()->write(json_encode($transaction));

    return $response->withHeader('Content-Type', 'application/json');
  }

  #[OA\Patch(
    path: "/transactions/{id}/reject",
    summary: "Rejeitar uma transação pendente",
    security: [["bearerAuth" => []]],
    tags: ["Transações"],
    parameters: [
      new OA\Parameter(
        name: "id",
        in: "path",
        required: true,
        schema: new OA\Schema(type: "integer"),
        example: 1
      )
    ],
    responses: [
      new OA\Response(
        response: 200,
        description: "Transação rejeitada com sucesso",
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: "id", type: "integer"),
            new OA\Property(property: "status", type: "string", example: "rejected")
          ]
        )
      ),
      new OA\Response(response: 400, description: "Transação já processada"),
      new OA\Response(response: 404, description: "Transação não encontrada"),
      new OA\Response(response: 401, description: "Não autenticado")
    ]
  )]
  public function reject(Request $request, Response $response, array $args)
  {
    $transactionId = $args['id'];
    $userId = $request->getAttribute('user_id');

    $transaction = Transaction::find($transactionId);

    if (!$transaction || $transaction->to_user_id != $userId) {
      $response->getBody()->write(json_encode(['error' => 'Transaction not found']));

      return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    if ($transaction->status !== 'pending') {
      $response->getBody()->write(json_encode(['error' => 'Transaction already processed']));

      return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $transaction->status = 'rejected';
    $transaction->save();

    $response->getBody()->write(json_encode($transaction));

    return $response->withHeader('Content-Type', 'application/json');
  }

  #[OA\Get(
    path: "/transactions/incoming",
    summary: "Listar transações pendentes recebidas",
    security: [["bearerAuth" => []]],
    tags: ["Transações"],
    responses: [
      new OA\Response(
        response: 200,
        description: "Lista de transações pendentes",
        content: new OA\JsonContent(
          type: "array",
          items: new OA\Items(
            properties: [
              new OA\Property(property: "id", type: "integer"),
              new OA\Property(property: "from_user_id", type: "integer"),
              new OA\Property(property: "to_user_id", type: "integer"),
              new OA\Property(property: "hours", type: "number"),
              new OA\Property(property: "description", type: "string"),
              new OA\Property(property: "status", type: "string", example: "pending")
            ]
          )
        )
      ),
      new OA\Response(response: 401, description: "Não autenticado")
    ]
  )]
  public function incoming(Request $request, Response $response)
  {
    $userId = $request->getAttribute('user_id');

    $transactions = Transaction::where('to_user_id', $userId)->where('status', 'pending')->get();

    $response->getBody()->write(json_encode($transactions));

    return $response->withHeader('Content-Type', 'application/json');
  }

  #[OA\Get(
    path: "/transactions/available-users",
    summary: "Listar usuários disponíveis para transação",
    security: [["bearerAuth" => []]],
    tags: ["Transações"],
    responses: [
      new OA\Response(
        response: 200,
        description: "Lista de usuários disponíveis",
        content: new OA\JsonContent(
          type: "array",
          items: new OA\Items(
            properties: [
              new OA\Property(property: "id", type: "integer", example: 2),
              new OA\Property(property: "name", type: "string", example: "Maria Silva")
            ]
          )
        )
      ),
      new OA\Response(response: 401, description: "Não autenticado")
    ]
  )]
  public function getAvailableUsers(Request $request, Response $response)
  {
    $userId = $request->getAttribute('user_id');

    $users = User::where('id', '!=', $userId)
      ->select('id', 'name')
      ->get();

    $response->getBody()->write(json_encode($users));

    return $response->withHeader('Content-Type', 'application/json');
  }
}