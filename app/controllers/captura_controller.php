<?php
// ======================================================================
//  captura_controller.php
//  Versão: captura → interpreta HTML → salva pesagens → retorna lista
// ======================================================================

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';
$db = Database::connect();

// Garantir operação + período na sessão
if (!isset($_SESSION['operacao']) || !isset($_SESSION['periodo'])) {
    echo "<div class='alert alert-warning'>Operação ou período não encontrado.</div>";
    exit;
}

$op  = $_SESSION['operacao'];
$per = $_SESSION['periodo'];

$navio   = $op['navio']   ?? '';
$inicio  = $per['inicio'] ?? '';
$fim     = $per['fim']    ?? '';

if (!$navio || !$inicio || !$fim) {
    echo "<div class='alert alert-danger'>Dados incompletos para captura.</div>";
    exit;
}

// ======================================================================
// 1) MONTAR URL PARA conferentes.app.br/teste.php
// ======================================================================

$baseUrl = "https://conferentes.app.br/teste.php";

$inicioFormatado = str_replace('T', ' ', $inicio);
$fimFormatado    = str_replace('T', ' ', $fim);

$query = http_build_query([
    'navio'   => $navio,
    'inicio'  => $inicioFormatado,
    'termino' => $fimFormatado
]);

$url = $baseUrl . '?' . $query;

// ======================================================================
// 2) FAZER REQUISIÇÃO
// ======================================================================
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

if ($response === false || $httpCode !== 200) {
    echo "<div class='alert alert-danger'>
            Falha ao consultar servidor externo.<br>
            HTTP: {$httpCode}<br>
            Erro cURL: " . htmlspecialchars($curlErr) . "
          </div>";
    exit;
}

// ======================================================================
// 3) PARSEAR O HTML RETORNADO
//     Procuramos linhas <tr> contendo os dados das pesagens.
// ======================================================================

$dom = new DOMDocument();
libxml_use_internal_errors(true);
$dom->loadHTML($response);
libxml_clear_errors();

$rows = $dom->getElementsByTagName('tr');

$pesagens = [];
foreach ($rows as $tr) {
    $cols = $tr->getElementsByTagName('td');
    if ($cols->length < 7) {
        continue; // linhas que não são pesagem
    }

    // O teste.php retorna normalmente nesta ordem:
    // 0=Ticket, 1=Empresa, 2=Placa, 3=Carga, 4=Peso, 5=Data/Hora, 6=Operação, 7=Origem/Dest.
    $ticket = trim($cols->item(0)->textContent);

    // Sem ticket → não grava
    if ($ticket === '') continue;

    $pesagens[] = [
        'ticket'      => $ticket,
        'empresa'     => trim($cols->item(1)->textContent),
        'placa'       => trim($cols->item(2)->textContent),
        'carga'       => trim($cols->item(3)->textContent),
        'peso'        => trim($cols->item(4)->textContent),
        'horario'     => trim($cols->item(5)->textContent),
        'operacao'    => trim($cols->item(6)->textContent),
        'origem_dest' => trim($cols->item(7)->textContent ?? ''),
    ];
}

// ======================================================================
// 4) SALVAR NO BANCO (periodo_id vem da sessão)
// ======================================================================

$periodoId = $per['id'] ?? null;
if (!$periodoId) {
    echo "<div class='alert alert-danger'>Erro interno: período sem ID.</div>";
    exit;
}

// Limpa tudo do período antes de inserir novamente
$db->prepare("DELETE FROM pesagens WHERE periodo_id = ?")->execute([$periodoId]);

$insert = $db->prepare("
    INSERT OR IGNORE INTO pesagens 
    (periodo_id, ticket, empresa, placa, carga, peso, horario, operacao, origem_dest)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
");

foreach ($pesagens as $p) {
    $insert->execute([
        $periodoId,
        $p['ticket'],
        $p['empresa'],
        $p['placa'],
        $p['carga'],
        $p['peso'],
        $p['horario'],
        $p['operacao'],
        $p['origem_dest']
    ]);
}

// ======================================================================
// 5) LISTAR O QUE FOI SALVO
// ======================================================================

$stmt = $db->prepare("SELECT * FROM pesagens WHERE periodo_id = ? ORDER BY horario ASC");
$stmt->execute([$periodoId]);
$lista = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Pesagens capturadas</h3>";

if (empty($lista)) {
    echo "<div class='alert alert-warning'>Nenhuma pesagem encontrada.</div>";
    exit;
}

echo "<table class='table table-bordered table-sm'>";
echo "<thead><tr>
        <th>Ticket</th>
        <th>Placa</th>
        <th>Empresa</th>
        <th>Carga</th>
        <th>Peso</th>
        <th>Horário</th>
      </tr></thead><tbody>";

foreach ($lista as $l) {
    echo "<tr>
            <td>{$l['ticket']}</td>
            <td>{$l['placa']}</td>
            <td>{$l['empresa']}</td>
            <td>{$l['carga']}</td>
            <td>{$l['peso']}</td>
            <td>{$l['horario']}</td>
          </tr>";
}

echo "</tbody></table>";
