<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

$db->exec("ALTER TABLE navios ADD COLUMN decks TEXT");

echo "OK";
