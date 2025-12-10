<?php
// app/controllers/captura_controller.php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();

// Conferência: precisamos ter operação e período na sessão
if (!isset($_SESSION['operacao']) || !isset($_SESSION['periodo'])) {
    echo "<div class='alert alert-warning'>Operação ou período não encontrado na sessão.</div>";
    exit;
}

$op  = $_SESSION['operacao'];
$per = $_SESSION['periodo'];

// Pegamos os dados conforme definimos ao criar operação/período
$navio   = $op['navio']   ?? '';
$produto = $op['produto'] ?? '';   // pode ser vazio, mas a chave precisa existir
$recinto = $op['recinto'] ?? '';   // idem

$inicio  = $per['inicio'] ?? '';
$fim     = $per['fim']    ?? '';

// Segurança básica: se faltar navio/início/fim, não chama nada
if (!$navio || !$inicio || !$fim) {
    echo "<div class='alert alert-danger'>Dados incompletos para consulta.</div>";
    exit;
}

// O datetime-local viria como 2025-12-07T07:00 — o teste.php espera um espaço
$inicioFormatado = str_replace('T', ' ', $inicio);
$fimFormatado    = str_replace('T', ' ', $fim);

// Monta a URL para o teste.php no conferentes.app.br
$baseUrl = "https://conferentes.app.br/teste.php";

$query = http_build_query([
    'navio'   => $navio,
    'inicio'  => $inicioFormatado,
    'termino' => $fimFormatado,
    // mesmo que estes não sejam usados para filtrar,
    // enviamos as chaves vazias para evitar "Undefined array key"
    'produto' => $produto,
    'recinto' => $recinto,
]);

$url = $baseUrl . '?' . $query;

// Faz a requisição servidor → conferentes.app.br
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

// Tratamento básico de erro
if ($response === false || $httpCode !== 200) {
    echo "<div class='alert alert-danger'>
            Falha ao consultar teste.php<br>
            HTTP: {$httpCode}<br>
            Erro cURL: " . htmlspecialchars($curlErr) . "
          </div>";
    exit;
}

// Devolve o HTML que veio do teste.php (tabela de pesagens)
echo $response;
