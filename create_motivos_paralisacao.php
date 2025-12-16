<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

$db->exec("
    CREATE TABLE motivos_paralisacao (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        ativo INTEGER DEFAULT 1,
        criado_em TEXT DEFAULT CURRENT_TIMESTAMP
    );
");

header('Content-Type: text/plain; charset=utf-8');
echo "Tabela motivos_paralisacao criada com sucesso\n";
