<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

$db->exec("
    CREATE TABLE IF NOT EXISTS pesagens (
        id INTEGER PRIMARY KEY AUTOINCREMENT,

        periodo_id INTEGER NOT NULL,

        ticket INTEGER NOT NULL,
        placa TEXT,
        empresa TEXT,

        data_hora TEXT NOT NULL,
        peso_liquido REAL NOT NULL,

        carga TEXT,
        operacao TEXT,

        terno INTEGER,
        equipamento TEXT,
        porao INTEGER,
        deck TEXT,
        origem_destino TEXT,

        criado_em TEXT DEFAULT (datetime('now')),

        UNIQUE (periodo_id, ticket)
    )
");

echo "Tabela pesagens criada/confirmada com sucesso.";
