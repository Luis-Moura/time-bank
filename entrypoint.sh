#!/bin/bash
set -e

php migrate.php || echo "⚠️ Migrations falharam, iniciando Apache mesmo assim..."
exec apache2-foreground
