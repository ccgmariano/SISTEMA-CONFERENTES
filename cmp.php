<?php

return [
    'tabela' => 'motivos_paralisacao',
    'titulo' => 'Motivos de Paralisação',
    'campos' => [
        'nome' => [
            'label' => 'Motivo',
            'type' => 'text',
            'required' => true
        ],
        'ativo' => [
            'label' => 'Ativo',
            'type' => 'text'
        ]
    ]
];
<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

$db->exec("
CREATE TABLE IF NOT EXISTS periodo_funcoes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    periodo_id INTEGER NOT NULL,
    funcao_id INTEGER NOT NULL
);
");

$db->exec("
CREATE TABLE IF NOT EXISTS periodo_conferentes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    periodo_funcao_id INTEGER NOT NULL,
    associado_id INTEGER NOT NULL
);
");

echo 'OK';
