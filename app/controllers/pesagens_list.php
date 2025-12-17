<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';
$db = Database::connect();

$periodo_id = isset($_GET['periodo_id']) ? (int)$_GET['periodo_id'] : 0;
if ($periodo_id <= 0) {
    die('Período inválido.');
}

$stmt = $db->prepare("
    SELECT
        id,
        ticket,
        placa,
        peso_liquido,
        data_hora,
        porao,
        deck,
        equipamento
    FROM pesagens
    WHERE periodo_id = ?
    ORDER BY data_hora
");
$stmt->execute([$periodo_id]);
$pesagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($pesagens)) {
    echo "<p><em>Nenhuma pesagem conferida ainda.</em></p>";
    exit;
}

echo "<table border='1' cellpadding='6' cellspacing='0' width='100%'>";
echo "<tr>
        <th>Ticket</th>
        <th>Placa</th>
        <th>Peso Líquido</th>
        <th>Data/Hora</th>
        <th>Porão</th>
        <th>Deck</th>
        <th>Equipamento</th>
      </tr>";

foreach ($pesagens as $p) {
    echo "<tr>
            <td>{$p['ticket']}</td>
            <td>{$p['placa']}</td>
            <td>{$p['peso_liquido']}</td>
            <td>{$p['data_hora']}</td>
            <td>{$p['porao']}</td>
            <td>{$p['deck']}</td>
            <td>{$p['equipamento']}</td>
          </tr>";
}

echo "</table>";
