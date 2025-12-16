<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

$db->exec("
    CREATE TABLE IF NOT EXISTS operadores_portuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nome TEXT NOT NULL,
        razao_social TEXT,
        cnpj TEXT,
        whatsapp_group_id TEXT,
        ativo INTEGER DEFAULT 1,
        criado_em TEXT DEFAULT CURRENT_TIMESTAMP
    );
");

$db->exec("
    CREATE TABLE IF NOT EXISTS contatos_operadores (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        operador_id INTEGER NOT NULL,
        nome TEXT NOT NULL,
        email TEXT NOT NULL,
        celular TEXT,
        ativo INTEGER DEFAULT 1,
        criado_em TEXT DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (operador_id) REFERENCES operadores_portuarios(id)
    );
");

echo "Tabelas operadores_portuarios e contatos_operadores criadas com sucesso";
