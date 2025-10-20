<?php
use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use App\Handlers\HttpErrorHandler;

require __DIR__ . '/../vendor/autoload.php';

//dotenv
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

//database
require __DIR__ . '/../src/Config/database.php';

$app = AppFactory::create();

//base path
$app->setBasePath('/api/v1');

//rotas
require __DIR__ . '/../src/Routes/routes.php';

//configurações de erro
$displayErrorDetails = true;
$logErrors = true;
$logErrorDetails = true;

$errorMiddleware = new ErrorMiddleware(
    $app->getCallableResolver(),
    $app->getResponseFactory(),
    $displayErrorDetails,
    $logErrors,
    $logErrorDetails
);

$errorHandler = new HttpErrorHandler(
    $app->getCallableResolver(),
    $app->getResponseFactory()
);

$errorMiddleware->setDefaultErrorHandler($errorHandler);

$app->add($errorMiddleware);

$app->run();