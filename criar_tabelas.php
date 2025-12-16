<?php
require_once __DIR__ . '/app/database.php';

try {
    $db = Database::connect();

    echo "<pre>";

    // -----------------------------
    // TABELA OPERACOES
    // -----------------------------
    $db->exec("
        CREATE TABLE IF NOT EXISTS operacoes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            empresa TEXT NOT NULL,
            navio TEXT NOT NULL,
            produto TEXT NOT NULL,
            recinto TEXT NOT NULL,
            tipo_operacao TEXT NOT NULL,
            criado_em TEXT
        );
    ");
    echo "Tabela 'operacoes' OK\n";

    // -----------------------------
    // TABELA PERIODOS
    // -----------------------------
    $db->exec("
        CREATE TABLE IF NOT EXISTS periodos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            operacao_id INTEGER NOT NULL,
            data TEXT NOT NULL,
            inicio TEXT NOT NULL,
            fim TEXT NOT NULL,
            criado_em TEXT,
            FOREIGN KEY (operacao_id) REFERENCES operacoes(id)
        );
    ");
    echo "Tabela 'periodos' OK\n";

    // -----------------------------
    // TABELA ASSOCIADOS
    // -----------------------------
    $db->exec("
        CREATE TABLE IF NOT EXISTS associados (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL,
            codigo TEXT,
            observacoes TEXT,
            ativo INTEGER DEFAULT 1,
            criado_em TEXT DEFAULT CURRENT_TIMESTAMP
        );
    ");
    echo "Tabela 'associados' OK\n";

    echo "\nTodas as tabelas foram criadas/validadas com sucesso.";

    echo "</pre>";

} catch (Exception $e) {
    echo "<h2>Erro ao criar tabelas:</h2>";
    echo "<pre>" . $e->getMessage() . "</pre>";
}
