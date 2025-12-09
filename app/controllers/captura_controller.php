<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();

// Checa sessão (operação + período)
if (!isset($_SESSION['operacao']) || !isset($_SESSION['periodo'])) {
    echo "<div class='alert alert-warning'>Operação ou período não encontrado na sessão.</div>";
    exit;
}

$op  = $_SESSION['operacao'];
$per = $_SESSION['periodo'];

$navio  = $op['navio']   ?? '';
$inicio = $per['inicio'] ?? '';
$fim    = $per['fim']    ?? '';

if (!$navio || !$inicio || !$fim) {
    echo "<div class='alert alert-danger'>Dados incompletos para consulta.</div>";
    exit;
}

$inicioFormatado = str_replace('T', ' ', $inicio);
$fimFormatado    = str_replace('T', ' ', $fim);

// URL sem produto e sem recinto
$baseUrl = "https://conferentes.app.br/teste.php";
$query = http_build_query([
    'navio'   => $navio,
    'inicio'  => $inicioFormatado,
    'termino' => $fimFormatado,
]);

$url = $baseUrl . '?' . $query;

// Requisição ao conferentes.app.br
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
            Falha ao consultar conferentes.app.br<br>
            HTTP: {$httpCode}<br>
            Erro cURL: " . htmlspecialchars($curlErr) . "
          </div>";
    exit;
}

// Simplesmente exibe o HTML retornado
echo $response;
