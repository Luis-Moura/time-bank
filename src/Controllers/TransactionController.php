<?php

namespace App\Controllers;

use App\Models\Transaction;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TransactionController
{
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

  public function getUserTransactions(Request $request, Response $response)
  {
    $userId = $request->getAttribute('user_id');

    $transactions = Transaction::where('from_user_id', $userId)
      ->orWhere('to_user_id', $userId)
      ->get();

    $response->getBody()->write(json_encode($transactions));
    return $response->withHeader('Content-Type', 'application/json');
  }

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

  public function incoming(Request $request, Response $response)
  {
    $userId = $request->getAttribute('user_id');

    $transactions = Transaction::where('to_user_id', $userId)->get();

    $response->getBody()->write(json_encode($transactions));

    return $response->withHeader('Content-Type', 'application/json');
  }
}