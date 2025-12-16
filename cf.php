<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

$db->exec("
    CREATE TABLE IF NOT EXISTS funcoes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        ativo INTEGER DEFAULT 1,
        criado_em TEXT DEFAULT CURRENT_TIMESTAMP
    );
");

echo "Tabela funcoes criada com sucesso";
