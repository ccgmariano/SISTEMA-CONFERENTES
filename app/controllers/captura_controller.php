<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/services/poseidon_login.php';

$db = Database::connect();

// ======================================================
// 1. RECEBE O ID DO PERÍODO
// ======================================================
$periodoId = isset($_GET['periodo_id']) ? (int)$_GET['periodo_id'] : 0;

if ($periodoId <= 0) {
    echo "<div class='alert alert-danger'>Período inválido.</div>";
    exit;
}

// ======================================================
// 2. BUSCA PERÍODO
// ======================================================
$stmt = $db->prepare("SELECT * FROM periodos WHERE id = ?");
$stmt->execute([$periodoId]);
$periodo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    echo "<div class='alert alert-danger'>Período não encontrado.</div>";
    exit;
}

// ======================================================
// 3. BUSCA OPERAÇÃO
// ======================================================
$stmt = $db->prepare("SELECT * FROM operacoes WHERE id = ?");
$stmt->execute([$periodo['operacao_id']]);
$operacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$operacao) {
    echo "<div class='alert alert-danger'>Operação vinculada não encontrada.</div>";
    exit;
}

// ======================================================
// 4. CONVERTE DATAS PARA O FORMATO EXIGIDO PELO POSEIDON
// ======================================================
function brDateTime($dataISO, $hora)
{
    [$y, $m, $d] = explode('-', $dataISO);
    return "{$d}/{$m}/{$y} {$hora}";
}

$dataInicioBR = brDateTime($periodo['data'], $periodo['inicio']);

// Ajusta virada de dia (meia-noite)
$dataFimISO = $periodo['data'];
if ($periodo['fim'] === "00:59" || $periodo['fim'] === "00:00") {
    $dataFimISO = date('Y-m-d', strtotime($periodo['data'] . ' +1 day'));
}
$dataFimBR = brDateTime($dataFimISO, $periodo['fim']);

// ======================================================
// 5. GARANTE LOGIN NO POSEIDON
// ======================================================
if (!poseidon_login()) {
    echo "<div class='alert alert-danger'>Falha no login do Poseidon.</div>";
    exit;
}

// ======================================================
// 6. MONTA O POST EXATAMENTE IGUAL AO QUE O POSEIDON ESPERA
// ======================================================
$postFields = http_build_query([
    '_method'     => 'POST',
    'cpf'         => POSEIDON_CPF,
    'data_inicio' => $dataInicioBR,
    'data_fim'    => $dataFimBR,
    'navio'       => $operacao['navio'],
    'produto'     => '',     // deixamos vazio igual ao navegador
    'recinto'     => ''      // deixamos vazio igual ao navegador
]);

$url = "https://poseidon.pimb.net.br/consultas/view/83";

// ======================================================
// 7. EXECUTA POST AUTENTICADO
// ======================================================
$ch = curl_init($url);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

curl_setopt($ch, CURLOPT_COOKIEJAR,  POSEIDON_COOKIE_JAR);
curl_setopt($ch, CURLOPT_COOKIEFILE, POSEIDON_COOKIE_JAR);

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$code     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err      = curl_error($ch);

curl_close($ch);

// ======================================================
// 8. ERROS
// ======================================================
if ($response === false || $code !== 200) {
    echo "<div class='alert alert-danger'>
            Erro ao consultar Poseidon<br>
            HTTP: {$code}<br>
            Erro: {$err}
          </div>";
    exit;
}

// ======================================================
// 9. DEVOLVE A PÁGINA EXATAMENTE COMO O POSEIDON RESPONDE
// ======================================================
echo $response;
