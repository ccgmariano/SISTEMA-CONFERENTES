<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS operacoes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            empresa TEXT,
            navio TEXT,
            produto TEXT,
            recinto TEXT,
            tipo_operacao TEXT,
            criado_em TEXT
        );
    ");

    echo "Tabelas criadas/atualizadas com sucesso.";

} catch (Exception $e) {
    echo "Erro ao criar tabelas:<br>";
    echo $e->getMessage();
}
