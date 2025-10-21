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

  public function updateSkill(Request $request, Response $response, array $args)
  {
    $skillId = $args['id'];

    $skill = Skill::find($skillId);
    $data = $request->getParsedBody();
    $name = $data['name'] ?? null;
    $skillLevel = $data['skill_level'] ?? null;

    if (!$skill) {
      $response->getBody()->write(json_encode(['error' => 'Skill not found']));

      return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    if ($skill->user_id != $request->getAttribute('user_id')) {
      $response->getBody()->write(json_encode(['error' => 'Unauthorized']));

      return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
    }

    if (!$name || !$skillLevel) {
      $response->getBody()->write(json_encode(['error' => 'Missing fields']));

      return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
    }

    $skill->name = $name;
    $skill->skill_level = $skillLevel;
    $skill->save();

    $response->getBody()->write(json_encode($skill));

    return $response->withHeader('Content-Type', 'application/json');
  }
}