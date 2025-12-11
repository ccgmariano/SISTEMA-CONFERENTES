<?php
// app/controllers/captura_controller.php

require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();

// Conferência: precisa existir operação e período na sessão
if (!isset($_SESSION['operacao']) || !isset($_SESSION['periodo'])) {
    echo "<div class='alert alert-warning'>Operação ou período não encontrado na sessão.</div>";
    exit;
}

$op  = $_SESSION['operacao'];
$per = $_SESSION['periodo'];

// Dados mínimos necessários
$navio  = $op['navio']  ?? '';
$inicio = $per['inicio'] ?? '';
$fim    = $per['fim']    ?? '';

if (!$navio || !$inicio || !$fim) {
    echo "<div class='alert alert-danger'>Dados incompletos para consulta.</div>";
    exit;
}

/*
 * Converte de ISO (2025-12-10 07:00)
 * para DD/MM/AAAA HH:MM (10/12/2025 07:00)
 */
function isoToBRDateTime($iso)
{
    if (!str_contains($iso, ' ')) {
        return '';
    }

    [$data, $hora] = explode(' ', $iso);
    [$ano, $mes, $dia] = explode('-', $data);

    return "{$dia}/{$mes}/{$ano} {$hora}";
}

$inicioBR = isoToBRDateTime($inicio);
$fimBR    = isoToBRDateTime($fim);

// Monta URL da consulta real
$baseUrl = "https://conferentes.app.br/teste.php";
$query = http_build_query([
    'navio'   => $navio,
    'inicio'  => $inicioBR,
    'termino' => $fimBR,
]);

$url = $baseUrl . '?' . $query;

// Requisição servidor → conferentes.app.br
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlErr  = curl_error($ch);
curl_close($ch);

// Erro de consulta
if ($response === false || $httpCode !== 200) {
    echo "<div class='alert alert-danger'>
            Falha ao consultar Poseidon<br>
            HTTP: {$httpCode}<br>
            Erro cURL: " . htmlspecialchars($curlErr) . "
          </div>";
    exit;
}

// Retorna o HTML exatamente como recebido
echo $response;
