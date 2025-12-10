<?php
// criar_tabelas.php
require_once __DIR__ . '/app/database.php';

try {
    $db = Database::connect();

    //
    // 1) Tabela de usuários (já existia – mantenho aqui como exemplo)
    //
    $db->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nome TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            senha TEXT NOT NULL,
            criado_em TEXT DEFAULT (datetime('now'))
        );
    ");

    //
    // 2) Tabela de operações (já existia)
    //
    $db->exec("
        CREATE TABLE IF NOT EXISTS operacoes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            empresa       TEXT NOT NULL,
            navio         TEXT NOT NULL,
            produto       TEXT,
            recinto       TEXT,
            tipo_operacao TEXT NOT NULL,
            criado_em     TEXT DEFAULT (datetime('now'))
        );
    ");

    //
    // 3) Tabela de períodos (já existia)
    //
    $db->exec("
        CREATE TABLE IF NOT EXISTS periodos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            operacao_id INTEGER NOT NULL,
            inicio      TEXT NOT NULL,  -- ex: '07:00'
            fim         TEXT NOT NULL,  -- ex: '12:59'
            criado_em   TEXT DEFAULT (datetime('now')),
            FOREIGN KEY (operacao_id) REFERENCES operacoes(id)
        );
    ");

    //
    // 4) NOVA TABELA: pesagens
    //
    // Cada linha desta tabela representa uma pesagem de caminhão
    // capturada para um PERÍODO específico.
    //
    $db->exec("
        CREATE TABLE IF NOT EXISTS pesagens (
            id INTEGER PRIMARY KEY AUTOINCREMENT,

            periodo_id INTEGER NOT NULL,

            ticket      TEXT,   -- chave única da pesagem
            empresa     TEXT,
            placa       TEXT,
            carga       TEXT,   -- produto/carga textual
            peso        REAL,   -- peso líquido ou principal que você quiser
            horario     TEXT,   -- horário da pesagem (texto vindo do relatório)
            operacao    TEXT,   -- ex: 'Carga - Navio'
            origem_dest TEXT,   -- 'EXTERNO', etc.

            bruto       REAL,   -- se mais tarde quisermos guardar bruto
            tara        REAL,   -- tara
            liquido     REAL,   -- peso líquido

            criado_em   TEXT DEFAULT (datetime('now')),

            FOREIGN KEY (periodo_id) REFERENCES periodos(id)
        );
    ");

    //
    // 5) ÍNDICE para evitar duplicatas por período+ticket
    //
    $db->exec("
        CREATE UNIQUE INDEX IF NOT EXISTS idx_pesagens_periodo_ticket
        ON pesagens (periodo_id, ticket);
    ");

    echo "Tabelas criadas/atualizadas com sucesso.";

} catch (PDOException $e) {
    echo "Erro ao criar/atualizar tabelas:<br>";
    echo htmlspecialchars($e->getMessage());
}
