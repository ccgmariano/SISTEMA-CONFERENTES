
<?php
require_once __DIR__ . '/app/database.php';

$db = Database::connect();

$cols = $db->query("PRAGMA table_info(navios)")->fetchAll(PDO::FETCH_COLUMN, 1);

if (!in_array('num_poroes', $cols)) {
    $db->exec("ALTER TABLE navios ADD COLUMN num_poroes INTEGER");
    echo "Coluna num_poroes adicionada\n";
}

if (!in_array('decks', $cols)) {
    $db->exec("ALTER TABLE navios ADD COLUMN decks TEXT");
    echo "Coluna decks adicionada\n";
}

echo "Navios atualizado com sucesso";
