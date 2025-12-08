<?php
require_once __DIR__ . '/app/database.php';

try {
    $db = Database::connect();

    // tabela operações
    $db->exec("
        CREATE TABLE IF NOT EXISTS operacoes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            empresa TEXT,
            navio TEXT,
            produto TEXT,
            recinto TEXT,
            tipo_operacao TEXT,
            criado_em TEXT
        )
    ");

    // tabela períodos
    $db->exec("
        CREATE TABLE IF NOT EXISTS periodos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            operacao_id INTEGER,
            inicio TEXT,
            fim TEXT,
            FOREIGN KEY (operacao_id) REFERENCES operacoes(id)
        )
    ");

    echo "Tabelas criadas/atualizadas com sucesso.";

} catch (Exception $e) {
    echo "Erro ao criar tabelas:<br>";
    echo $e->getMessage();
}
