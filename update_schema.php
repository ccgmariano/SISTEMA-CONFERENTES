<?php
require_once __DIR__ . '/app/database.php';

try {
    $db = Database::connect();

    // Tenta adicionar a coluna DATA na tabela PERIODOS
    $db->exec("ALTER TABLE periodos ADD COLUMN data TEXT");

    echo "<h2>Coluna 'data' criada com sucesso!</h2>";

} catch (Exception $e) {

    // Se a coluna já existir, o SQLite dá erro — tratamos isso aqui:
    if (strpos($e->getMessage(), "duplicate column name") !== false) {
        echo "<h2>A coluna 'data' já existe. Nada a fazer.</h2>";
    } else {
        echo "<h2>Erro ao alterar tabela:</h2>";
        echo "<pre>" . $e->getMessage() . "</pre>";
    }
}
?>
