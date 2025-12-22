<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';

$db = Database::connect();

$stmt = $db->prepare("DELETE FROM pesagens WHERE ticket = ?");
$stmt->execute(['999999']);

echo "<pre>OK. Linhas removidas: " . $stmt->rowCount() . "</pre>";
