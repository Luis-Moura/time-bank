<?php

namespace App\Controllers;

use App\Models\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Attributes as OA;

class UserController
{
  public function me(Request $request, Response $response)
  {
    $userId = $request->getAttribute('user_id');
    $user = User::find($userId);

    $response->getBody()->write(json_encode($user));

    return $response->withHeader('Content-Type', 'application/json');
  }
}
