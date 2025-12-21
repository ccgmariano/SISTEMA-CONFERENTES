<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';
$db = Database::connect();

// ======================================================
// 1. VALIDAR PERIODO_ID
// ======================================================
$periodoId = isset($_GET['periodo_id']) ? (int)$_GET['periodo_id'] : 0;
if ($periodoId <= 0) {
    echo "<div class='alert alert-danger'>Período inválido.</div>";
    exit;
}

// ======================================================
// 2. BUSCAR PERÍODO + OPERAÇÃO
// ======================================================
$stmt = $db->prepare("
    SELECT p.inicio, p.fim, o.navio
    FROM periodos p
    JOIN operacoes o ON o.id = p.operacao_id
    WHERE p.id = ?
");
$stmt->execute([$periodoId]);
$dados = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$dados) {
    echo "<div class='alert alert-danger'>Período não encontrado.</div>";
    exit;
}

// ======================================================
// 3. DATAS PARA FORMATO BR
// ======================================================
function dataParaBR($dataISO) {
    $dt = DateTime::createFromFormat('Y-m-d H:i', $dataISO);
    return $dt ? $dt->format('d/m/Y H:i') : '';
}

$dataInicio = dataParaBR($dados['inicio']);
$dataFim    = dataParaBR($dados['fim']);
$navio      = trim($dados['navio']);

// ======================================================
// 4. BUSCAR TICKETS JÁ CONFERIDOS
// ======================================================
$stmt = $db->prepare("
    SELECT ticket
    FROM pesagens_conferidas
    WHERE periodo_id = ?
");
$stmt->execute([$periodoId]);
$ticketsConferidos = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Converte para lookup rápido
$mapConferidos = array_flip($ticketsConferidos);

// ======================================================
// 5. CHAMAR BACKEND NODE (POSEIDON)
// ======================================================
$urlNode = "https://sistema-conferentes-node.onrender.com/poseidon/pesagens";

$payload = json_encode([
    'data_inicio' => $dataInicio,
    'data_fim'    => $dataFim,
    'navio'       => $navio
]);

$ch = curl_init($urlNode);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json']
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    echo "<div class='alert alert-danger'>Erro ao consultar pesagens.</div>";
    exit;
}

$data = json_decode($response, true);
if (!$data || empty($data['ok'])) {
    echo "<div class='alert alert-danger'>Resposta inválida do servidor.</div>";
    exit;
}

// ======================================================
// 6. TABELA DE PESAGENS (COM LUPA CONDICIONAL)
// ======================================================
echo "<h4>Pesagens do período</h4>";
echo "<p><strong>Navio:</strong> {$navio}</p>";
echo "<p><strong>Período:</strong> {$dataInicio} → {$dataFim}</p>";
echo "<p><strong>Total:</strong> {$data['total']} registros</p>";

echo "<table border='1' cellpadding='6' cellspacing='0' width='100%'>";
echo "<tr>
        <th>Ação</th>
        <th>Ticket</th>
        <th>Placa</th>
        <th>Entrada</th>
        <th>Saída</th>
        <th>Peso Líquido</th>
      </tr>";

foreach ($data['registros'] as $r) {

    $ticket  = (string)$r['ticket_id'];
    $placa   = htmlspecialchars($r['placa']);
    $peso    = htmlspecialchars($r['peso_liquido']);
    $entrada = htmlspecialchars($r['entrada']);
    $saida   = htmlspecialchars($r['saida']);

    $jaConferida = isset($mapConferidos[$ticket]);

    // Estilo visual
    $styleLinha = $jaConferida
        ? "style='background:#f0f0f0;color:#2e7d32;'"
        : "";

    echo "<tr {$styleLinha}>";

    echo "<td style='text-align:center'>";

    if ($jaConferida) {
        // Lupa desabilitada
        echo "<span title='Pesagem já conferida'
                   style='opacity:0.4; cursor:not-allowed;'>🔍</span>";
    } else {
        // Lupa ativa
        echo "<button
                title='Conferir pesagem'
                onclick=\"abrirModalPesagem(
                    '{$ticket}',
                    '{$placa}',
                    '{$peso}'
                )\"
              >🔍</button>";
    }

    echo "</td>";

    echo "<td>{$ticket}</td>
          <td>{$placa}</td>
          <td>{$entrada}</td>
          <td>{$saida}</td>
          <td>{$peso}</td>";

    echo "</tr>";
}

echo "</table>";
