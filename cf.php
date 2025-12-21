<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

$sql = "
CREATE TABLE periodo_config_lancamentos (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    periodo_id INTEGER NOT NULL,

    terno INTEGER,
    equipamento_id INTEGER,
    porao INTEGER,
    deck TEXT,
    origem_destino_id INTEGER,

    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME
);
";

try {
    $db->exec($sql);
    echo "Tabela periodo_config_lancamentos criada com sucesso.";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
