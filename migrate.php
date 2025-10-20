<?php
require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require __DIR__ . '/src/Config/database/database.php';

$migrationsPath = __DIR__ . '/src/Config/database/migrations';

if (!is_dir($migrationsPath)) {
    echo "Pasta de migrations não encontrada!\n";
    exit(1);
}

$files = glob($migrationsPath . '/*.php');

if (empty($files)) {
    echo "Nenhuma migration encontrada!\n";
    exit(1);
}

echo "Executando migrations...\n\n";

foreach ($files as $file) {
    echo "Rodando: " . basename($file) . "\n";

    require_once $file;

    echo "✓ Concluída\n\n";
}

echo "Todas as migrations foram executadas com sucesso!\n";