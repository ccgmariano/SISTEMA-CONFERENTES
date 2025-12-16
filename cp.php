<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

$db->exec("
    CREATE TABLE IF NOT EXISTS paralisacoes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        periodo_id INTEGER NOT NULL,
        motivo TEXT NOT NULL,
        inicio TEXT NOT NULL,
        fim TEXT NOT NULL,
        observacao TEXT,
        criado_em TEXT DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (periodo_id) REFERENCES periodos(id)
    );
");

echo "Tabela paralisacoes criada com sucesso";
