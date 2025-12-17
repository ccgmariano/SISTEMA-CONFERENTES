<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

$db->exec("
    CREATE TABLE IF NOT EXISTS config_lancamentos (
        id INTEGER PRIMARY KEY AUTOINCREMENT,

        periodo_id INTEGER NOT NULL UNIQUE,

        porao INTEGER,
        deck TEXT,
        equipamento_id INTEGER,
        origem_destino_id INTEGER,

        criado_em TEXT DEFAULT (datetime('now'))
    )
");

echo 'Tabela config_lancamentos criada/confirmada com sucesso.';
