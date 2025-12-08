<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

try {

    // Criar tabela de operações
    $db->exec("
        CREATE TABLE IF NOT EXISTS operacoes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            empresa TEXT NOT NULL,
            tipo TEXT NOT NULL,
            navio TEXT NOT NULL,
            produto TEXT NOT NULL,
            recinto TEXT NOT NULL,
            criado_em TEXT NOT NULL
        );
    ");

    // Criar tabela de períodos
    $db->exec("
        CREATE TABLE IF NOT EXISTS periodos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            operacao_id INTEGER NOT NULL,
            inicio TEXT NOT NULL,
            fim TEXT NOT NULL,
            FOREIGN KEY (operacao_id) REFERENCES operacoes(id)
        );
    ");

    echo "<h2>Tabelas criadas com sucesso!</h2>";

} catch (Exception $e) {
    echo "<h2>Erro ao criar tabelas:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
