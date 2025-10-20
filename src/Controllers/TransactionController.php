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

    $transaction = Transaction::create([
      'from_user_id' => $userId,
      'to_user_id' => $data['to_user_id'],
      'hours' => $data['hours'],
      'description' => $data['description']
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
}