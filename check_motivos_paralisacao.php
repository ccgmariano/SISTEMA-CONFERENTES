<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

$res = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='motivos_paralisacao'");
$row = $res->fetch(PDO::FETCH_ASSOC);

header('Content-Type: text/plain; charset=utf-8');

if ($row) {
    echo "Tabela motivos_paralisacao EXISTE\n";
} else {
    echo "Tabela motivos_paralisacao NÃO existe\n";
}
