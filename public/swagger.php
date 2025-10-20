<?php
require __DIR__ . '/../vendor/autoload.php';

use OpenApi\Generator;

header('Content-Type: application/json');

$openapi = Generator::scan([__DIR__ . '/../src']);

echo $openapi->toJson();
