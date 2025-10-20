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

// Ordena alfabeticamente (garante ordem correta)
sort($files);

echo "Executando migrations...\n\n";
    
foreach ($files as $file) {
    echo "Rodando: " . basename($file) . "\n";

    try {
        require_once $file;
        echo "✓ Concluída\n\n";
    } catch (\Illuminate\Database\QueryException $e) {
        echo "⚠ Ignorado: " . $e->getMessage() . "\n\n";
    } catch (\Exception $e) {
        echo "Erro inesperado: " . $e->getMessage() . "\n\n";
    }
}

echo "Todas as migrations foram executadas!\n";
