<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

$tabelas = $db->query("
    SELECT name 
    FROM sqlite_master 
    WHERE type='table'
    ORDER BY name
")->fetchAll(PDO::FETCH_COLUMN);

echo "<pre>";
print_r($tabelas);
echo "</pre>";
