<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Skill;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Attributes as OA;

class SkillsController
{
  public function addSkills(Request $request, Response $response)
  {
    $userId = $request->getAttribute('user_id');
    $data = $request->getParsedBody();
    $name = $data['name'];
    $skillLevel = $data['skill_level'] ?? null;

    if (!$name || !$skillLevel) {
      $response->getBody()->write(json_encode(['error' => 'Missing fields']));

      return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $existingSkill = Skill::where('user_id', $userId)->where('name', $name)->first();

    if ($existingSkill) {
      $response->getBody()->write(json_encode(['error' => 'Skill already exists for user.']));

      return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $skill = Skill::create([
      'name' => $name,
      'skill_level' => $skillLevel,
      'user_id' => $userId
    ]);

    $response->getBody()->write(json_encode($skill));

    return $response->withHeader('Content-Type', 'application/json');
  }

  public function listSkills(Request $request, Response $response)
  {
    $userId = $request->getAttribute('user_id');
    $skills = Skill::where('user_id', $userId)->get();

    $response->getBody()->write(json_encode($skills));

    return $response->withHeader('Content-Type', 'application/json');
  }
}