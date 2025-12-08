<?php
// criar_tabelas.php na RAIZ do projeto

require_once __DIR__ . '/app/database.php';

try {
    $db = Database::connect();

    // Tabela de operações
    $db->exec("
        CREATE TABLE IF NOT EXISTS operacoes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            empresa       TEXT NOT NULL,
            navio         TEXT NOT NULL,
            produto       TEXT NOT NULL,
            recinto       TEXT NOT NULL,
            tipo          TEXT NOT NULL,
            criado_em     TEXT NOT NULL
        );
    ");

    // Tabela de períodos
    $db->exec("
        CREATE TABLE IF NOT EXISTS periodos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            operacao_id INTEGER NOT NULL,
            inicio      TEXT NOT NULL,
            fim         TEXT NOT NULL,
            criado_em   TEXT NOT NULL,
            FOREIGN KEY (operacao_id) REFERENCES operacoes(id)
        );
    ");

    echo "<h3>Tabelas criadas/atualizadas com sucesso.</h3>";
} catch (Exception $e) {
    echo "<h3>Erro ao criar tabelas:</h3>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
}
