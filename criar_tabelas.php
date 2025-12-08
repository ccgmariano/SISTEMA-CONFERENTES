<?php
require_once __DIR__ . '/app/database.php';

try {
    $db = Database::connect();

    $db->exec("
        CREATE TABLE IF NOT EXISTS operacoes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            empresa TEXT NOT NULL,
            navio TEXT NOT NULL,
            produto TEXT NOT NULL,
            recinto TEXT NOT NULL,
            tipo_operacao TEXT NOT NULL,
            criado_em TEXT NOT NULL
        );
    ");

    $db->exec("
        CREATE TABLE IF NOT EXISTS periodos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            operacao_id INTEGER NOT NULL,
            inicio TEXT NOT NULL,
            fim TEXT NOT NULL
        );
    ");

    echo "Tabelas criadas/atualizadas com sucesso.";

} catch (Exception $e) {
    echo "Erro ao criar tabelas:<br>" . $e->getMessage();
}
?>
