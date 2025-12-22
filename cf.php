<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';

$db = Database::connect();

$stmt = $db->query("
    SELECT periodo_id, ticket
    FROM pesagens
    ORDER BY criado_em DESC
    LIMIT 5
");

echo "<pre>";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
echo "</pre>";
