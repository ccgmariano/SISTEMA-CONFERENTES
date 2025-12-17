<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

// use um periodo_id REAL que exista
$periodo_id = 1;

$_POST = [
    'periodo_id'      => $periodo_id,
    'ticket'          => 999999,
    'placa'           => 'ABC1D23',
    'empresa'         => 'TESTE',
    'data_hora'       => '2025-01-01 10:00',
    'peso_liquido'    => 12345,
    'carga'           => 'CARGA TESTE',
    'operacao'        => 'DESCARGA',
    'terno'           => 1,
    'equipamento'     => 'GUINDASTE',
    'porao'           => 2,
    'deck'            => 'A',
    'origem_destino'  => 'PORTO TESTE'
];

require __DIR__ . '/app/controllers/pesagem_store.php';
