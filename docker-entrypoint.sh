#!/bin/sh
set -e

# Garantir que o Apache (www-data) possa escrever no disk
chown -R www-data:www-data /var/data/db || true

exec apache2-foreground
