<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';

$db = Database::connect();

// --------------------------------------------------
// 1. PERÍODO
// --------------------------------------------------
$periodoId = isset($_GET['periodo_id']) ? (int)$_GET['periodo_id'] : 0;
if ($periodoId <= 0) {
    echo "<div class='alert alert-danger'>Período inválido.</div>";
    exit;
}

// --------------------------------------------------
// 2. BUSCAR PERÍODO + OPERAÇÃO
// --------------------------------------------------
$stmt = $db->prepare("
    SELECT 
        p.inicio,
        p.fim,
        o.navio
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

// --------------------------------------------------
// 3. FORMATAR DATAS (POSEIDON)
// --------------------------------------------------
function dataBR($dt) {
    $d = DateTime::createFromFormat('Y-m-d H:i', $dt);
    return $d ? $d->format('d/m/Y H:i') : '';
}

$dataInicio = dataBR($dados['inicio']);
$dataFim    = dataBR($dados['fim']);
$navio      = trim($dados['navio']);

// --------------------------------------------------
// 4. CHAMAR NODE / POSEIDON
// --------------------------------------------------
$url = "https://sistema-conferentes-node.onrender.com/poseidon/pesagens";

$payload = json_encode([
    'data_inicio' => $dataInicio,
    'data_fim'    => $dataFim,
    'navio'       => $navio
]);

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => $payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER     => ['Content-Type: application/json']
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    echo "<div class='alert alert-danger'>Erro ao consultar pesagens.</div>";
    exit;
}

$data = json_decode($response, true);
if (!$data || empty($data['registros'])) {
    echo "<div class='alert alert-warning'>Nenhuma pesagem encontrada.</div>";
    exit;
}

// --------------------------------------------------
// 5. BUSCAR TICKETS JÁ CONFERIDOS
// --------------------------------------------------
$stmt = $db->prepare("
    SELECT ticket 
    FROM pesagens 
    WHERE periodo_id = ?
");
$stmt->execute([$periodoId]);
$ticketsConferidos = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'ticket');

// --------------------------------------------------
// 6. RENDERIZAÇÃO
// --------------------------------------------------
echo "<table border='1' cellpadding='6' cellspacing='0' width='100%'>";
echo "<tr>
        <th></th>
        <th>Ticket</th>
        <th>Placa</th>
        <th>Entrada</th>
        <th>Saída</th>
        <th>Peso Líquido</th>
      </tr>";

foreach ($data['registros'] as $r) {

    $jaConferida = in_array($r['ticket_id'], $ticketsConferidos);

    if ($jaConferida) {
        $icone = "<span style='color:#999'>🔍</span>";
        $style = "style='color:#999;background:#f4f4f4'";
    } else {
        $icone = "
            <button
                onclick=\"abrirModalPesagem(this)\"
                data-ticket=\"{$r['ticket_id']}\"
                data-placa=\"{$r['placa']}\"
                data-peso=\"{$r['peso_liquido']}\">
                🔍
            </button>";
        $style = "";
    }

    echo "<tr $style>
            <td>$icone</td>
            <td>{$r['ticket_id']}</td>
            <td>{$r['placa']}</td>
            <td>{$r['entrada']}</td>
            <td>{$r['saida']}</td>
            <td>{$r['peso_liquido']}</td>
          </tr>";
}

echo "</table>";
