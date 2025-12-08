<?php
require_once __DIR__ . '/database.php';

$db = Database::connect();

$db->exec("
    CREATE TABLE IF NOT EXISTS operacoes (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        empresa TEXT NOT NULL,
        navio TEXT NOT NULL,
        produto TEXT NOT NULL,
        recinto TEXT NOT NULL,
        tipo TEXT NOT NULL,
        criado_em TEXT NOT NULL
    )
");
