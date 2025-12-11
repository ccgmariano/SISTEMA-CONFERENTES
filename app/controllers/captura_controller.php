<?php 
// app/controllers/captura_controller.php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';
$db = Database::connect();

// ======================================================
// 0. CPF TEMPORÁRIO (será removido quando login real existir)
// ======================================================
$cpf = "01774863928";  // <<< CPF FIXO PARA TESTES

// ======================================================
// 1. RECEBE O ID DO PERÍODO PELA URL
// ======================================================
$periodoId = isset($_GET['periodo_id']) ? (int)$_GET['periodo_id'] : 0;

if ($periodoId <= 0) {
    echo "<div class='alert alert-danger'>Período inválido.</div>";
    exit;
}

// ======================================================
// 2. BUSCA O PERÍODO NO BANCO
// ======================================================
$stmt = $db->prepare("SELECT * FROM periodos WHERE id = ?");
$stmt->execute([$periodoId]);
$periodo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    echo "<div class='alert alert-danger'>Período não encontrado.</div>";
    exit;
}

// ======================================================
// 3. BUSCA A OPERAÇÃO CORRESPONDENTE
// ======================================================
$stmt = $db->prepare("SELECT * FROM operacoes WHERE id = ?");
$stmt->execute([$periodo['operacao_id']]);
$operacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$operacao) {
    echo "<div class='alert alert-danger'>Operação vinculada não encontrada.</div>";
    exit;
}

// ======================================================
// 4. DADOS NECESSÁRIOS PARA A CONSULTA
// ======================================================
$navio  = $operacao['navio'];
$data   = $periodo['data'];   // formato: YYYY-MM-DD
$inicio = $periodo['inicio']; // ex: 07:00
$fim    = $periodo['fim'];    // ex: 12:59

if (!$navio || !$data || !$inicio || !$fim) {
    echo "<div class='alert alert-danger'>Dados incompletos para consulta.</div>";
    exit;
}

// ======================================================
// 5. CONVERTE YYYY-MM-DD HH:MM PARA DD/MM/YYYY HH:MM
// ======================================================
function brDateTime($dataISO, $hora)
{
    [$y, $m, $d] = explode('-', $dataISO);
    return "{$d}/{$m}/{$y} {$hora}";
}

$inicioBR = brDateTime($data, $inicio);

// Caso o período atravesse a meia-noite (19:00 → 00:59)
if ($fim === "00:59" || $fim === "00:00") {
    $dataFim = date('Y-m-d', strtotime($data . ' +1 day'));
} elseif ($inicio === "19:00" && $fim === "00:59") {
    $dataFim = date('Y-m-d', strtotime($data . ' +1 day'));
} elseif ($inicio === "01:00" && $fim === "06:59") {
    $dataFim = $data; // madrugada já pertence ao próprio dia informado
} else {
    $dataFim = $data;
}

$fimBR = brDateTime($dataFim, $fim);

// ======================================================
// 6. MONTA A URL DA CONSULTA REAL (Poseidon via teste.php)
// ======================================================
$baseUrl = "https://conferentes.app.br/teste.php";

$query = http_build_query([
    'cpf'     => $cpf,
    'navio'   => $navio,
    'inicio'  => $inicioBR,
    'termino' => $fimBR,
]);

$url = $baseUrl . '?' . $query;

// ======================================================
// 7. REQUISIÇÃO AO SERVIDOR EXTERNO
// ======================================================
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);

curl_close($ch);

// ======================================================
// 8. ERRO NA CONSULTA
// ======================================================
if ($response === false || $httpCode !== 200) {
    echo "<div class='alert alert-danger'>
            Erro ao consultar Poseidon<br>
            HTTP: {$httpCode}<br>
            Erro cURL: " . htmlspecialchars($curlErr) . "
          </div>";
    exit;
}

// ======================================================
// 9. EXIBE HTML CRU (recebido do servidor externo)
// ======================================================
echo $response;
