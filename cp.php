<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

$cols = $db->query("PRAGMA table_info(pesagens)")->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($cols);
echo "</pre>";
