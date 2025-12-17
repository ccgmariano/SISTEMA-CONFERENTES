<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

$sql = "
CREATE TABLE IF NOT EXISTS origem_destino (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nome TEXT NOT NULL
);
";

$db->exec($sql);

// dados iniciais
$db->exec("
INSERT INTO origem_destino (nome) VALUES
('INTERNO'),
('EXTERNO'),
('MAXIPORT'),
('RETROÁREA');
");

echo "Tabela origem_destino criada com sucesso.";
