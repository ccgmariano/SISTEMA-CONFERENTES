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
$produto = $op['produto'] ?? '';
$recinto = $op['recinto'] ?? '';

$inicio  = $per['inicio'] ?? '';
$fim     = $per['fim']    ?? '';

// Segurança básica: se faltar algo, não chama nada
if (!$navio || !$produto || !$recinto || !$inicio || !$fim) {
    echo "<div class='alert alert-danger'>Dados incompletos para consulta.</div>";
    exit;
}

// O datetime-local vem como 2025-12-07T07:00 — o teste.php espera um espaço
$inicioFormatado = str_replace('T', ' ', $inicio);
$fimFormatado    = str_replace('T', ' ', $fim);

// Monta a URL exata que já testamos no navegador, só que agora no servidor
$baseUrl = "https://conferentes.app.br/teste.php";
$query = http_build_query([
    'navio'   => $navio,
    'inicio'  => $inicioFormatado,
    'termino' => $fimFormatado,
    'produto' => $produto,
    'recinto' => $recinto,
]);

$url = $baseUrl . '?' . $query;

// Faz a requisição servidor → conferentes.app.br (igual ao navegador, mas do Render)
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

// Neste primeiro momento vamos simplesmente devolver o HTML bruto
// recebido do teste.php, como fizemos no navegador.
// Mais tarde podemos "limpar", padronizar tabela, guardar no banco etc.

echo $response;
