<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

$db->exec("ALTER TABLE navios ADD COLUMN num_poroes INTEGER");
$db->exec("ALTER TABLE navios ADD COLUMN ativo INTEGER DEFAULT 1");

echo "OK";
