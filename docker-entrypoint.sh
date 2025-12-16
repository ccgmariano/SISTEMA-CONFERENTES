#!/bin/sh
set -e

# Ajustar permissão do disk para o Apache
chown -R www-data:www-data /var/data/db || true

# Executar o comando original
exec "$@"
