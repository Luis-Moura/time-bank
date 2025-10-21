<?php

namespace App\Controllers;

use App\Models\Skill;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use OpenApi\Attributes as OA;

class SkillsController
{
  #[OA\Post(
    path: "/skills",
    summary: "Adicionar nova habilidade",
    security: [["bearerAuth" => []]],
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\JsonContent(
        required: ["name", "skill_level"],
        properties: [
          new OA\Property(property: "name", type: "string", example: "PHP"),
          new OA\Property(property: "skill_level", type: "string", example: "avançado")
        ]
      )
    ),
    tags: ["Habilidades"],
    responses: [
      new OA\Response(
        response: 200,
        description: "Habilidade criada com sucesso",
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: "id", type: "integer", example: 1),
            new OA\Property(property: "name", type: "string", example: "PHP"),
            new OA\Property(property: "skill_level", type: "string", example: "avançado"),
            new OA\Property(property: "user_id", type: "integer", example: 1),
            new OA\Property(property: "created_at", type: "string", format: "date-time"),
            new OA\Property(property: "updated_at", type: "string", format: "date-time")
          ]
        )
      ),
      new OA\Response(response: 400, description: "Dados inválidos ou habilidade já existe"),
      new OA\Response(response: 401, description: "Não autenticado")
    ]
  )]
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

  #[OA\Get(
    path: "/skills",
    summary: "Listar todas as habilidades do usuário",
    security: [["bearerAuth" => []]],
    tags: ["Habilidades"],
    responses: [
      new OA\Response(
        response: 200,
        description: "Lista de habilidades",
        content: new OA\JsonContent(
          type: "array",
          items: new OA\Items(
            properties: [
              new OA\Property(property: "id", type: "integer", example: 1),
              new OA\Property(property: "name", type: "string", example: "PHP"),
              new OA\Property(property: "skill_level", type: "string", example: "avançado"),
              new OA\Property(property: "user_id", type: "integer", example: 1),
              new OA\Property(property: "created_at", type: "string", format: "date-time"),
              new OA\Property(property: "updated_at", type: "string", format: "date-time")
            ]
          )
        )
      ),
      new OA\Response(response: 401, description: "Não autenticado")
    ]
  )]
  public function listSkills(Request $request, Response $response)
  {
    $userId = $request->getAttribute('user_id');
    $skills = Skill::where('user_id', $userId)->get();

    $response->getBody()->write(json_encode($skills));

    return $response->withHeader('Content-Type', 'application/json');
  }

  #[OA\Get(
    path: "/skills/{id}",
    summary: "Obter habilidade por ID",
    security: [["bearerAuth" => []]],
    tags: ["Habilidades"],
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
        description: "Detalhes da habilidade",
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: "id", type: "integer", example: 1),
            new OA\Property(property: "name", type: "string", example: "PHP"),
            new OA\Property(property: "skill_level", type: "string", example: "avançado"),
            new OA\Property(property: "user_id", type: "integer", example: 1),
            new OA\Property(property: "created_at", type: "string", format: "date-time"),
            new OA\Property(property: "updated_at", type: "string", format: "date-time")
          ]
        )
      ),
      new OA\Response(response: 403, description: "Não autorizado"),
      new OA\Response(response: 404, description: "Habilidade não encontrada"),
      new OA\Response(response: 401, description: "Não autenticado")
    ]
  )]
  public function getSkillById(Request $request, Response $response, array $args)
  {
    $skillId = $args['id'];
    $skill = Skill::find($skillId);

    if ($skill->user_id != $request->getAttribute('user_id')) {
      $response->getBody()->write(json_encode(['error' => 'Unauthorized']));

      return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
    }

    if (!$skill) {
      $response->getBody()->write(json_encode(['error' => 'Skill not found']));

      return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    $response->getBody()->write(json_encode($skill));

    return $response->withHeader('Content-Type', 'application/json');
  }

  #[OA\Put(
    path: "/skills/{id}",
    summary: "Atualizar uma habilidade",
    security: [["bearerAuth" => []]],
    requestBody: new OA\RequestBody(
      required: true,
      content: new OA\JsonContent(
        required: ["name", "skill_level"],
        properties: [
          new OA\Property(property: "name", type: "string", example: "JavaScript"),
          new OA\Property(property: "skill_level", type: "string", example: "intermediário")
        ]
      )
    ),
    tags: ["Habilidades"],
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
        description: "Habilidade atualizada com sucesso",
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: "id", type: "integer", example: 1),
            new OA\Property(property: "name", type: "string", example: "JavaScript"),
            new OA\Property(property: "skill_level", type: "string", example: "intermediário"),
            new OA\Property(property: "user_id", type: "integer", example: 1),
            new OA\Property(property: "created_at", type: "string", format: "date-time"),
            new OA\Property(property: "updated_at", type: "string", format: "date-time")
          ]
        )
      ),
      new OA\Response(response: 400, description: "Dados inválidos"),
      new OA\Response(response: 403, description: "Não autorizado"),
      new OA\Response(response: 404, description: "Habilidade não encontrada"),
      new OA\Response(response: 401, description: "Não autenticado")
    ]
  )]
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

  #[OA\Delete(
    path: "/skills/{id}",
    summary: "Deletar uma habilidade",
    security: [["bearerAuth" => []]],
    tags: ["Habilidades"],
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
        description: "Habilidade deletada com sucesso",
        content: new OA\JsonContent(
          properties: [
            new OA\Property(property: "message", type: "string", example: "Skill deleted")
          ]
        )
      ),
      new OA\Response(response: 403, description: "Não autorizado"),
      new OA\Response(response: 404, description: "Habilidade não encontrada"),
      new OA\Response(response: 401, description: "Não autenticado")
    ]
  )]
  public function deleteSkill(Request $request, Response $response, array $args)
  {
    $skillId = $args['id'];

    $skill = Skill::find($skillId);

    if (!$skill) {
      $response->getBody()->write(json_encode(['error' => 'Skill not found']));

      return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    }

    if ($skill->user_id != $request->getAttribute('user_id')) {
      $response->getBody()->write(json_encode(['error' => 'Unauthorized']));

      return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
    }

    Skill::destroy($skill->id);

    $response->getBody()->write(json_encode(['message' => 'Skill deleted']));

    return $response->withHeader('Content-Type', 'application/json');
  }
}