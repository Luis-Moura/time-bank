<?php
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

//base path
$app->setBasePath('/api/v1');

//rotas
require __DIR__ .'/../src/Routes/routes.php';

$app->run();