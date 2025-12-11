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
// 4. MONTA DATAS EM FORMATO BR
// ======================================================
function brDateTime($dataISO, $hora)
{
    [$y, $m, $d] = explode('-', $dataISO);
    return "{$d}/{$m}/{$y} {$hora}";
}

$dataInicio = brDateTime($periodo['data'], $periodo['inicio']);

// Ajusta virada de dia (meia-noite)
$dataFimISO = $periodo['data'];
if ($periodo['fim'] === "00:59" || $periodo['fim'] === "00:00") {
    $dataFimISO = date('Y-m-d', strtotime($periodo['data'] . ' +1 day'));
}

$dataFim = brDateTime($dataFimISO, $periodo['fim']);

// ======================================================
// 5. URL REAL DO POSEIDON PARA PEGAR AS PESAGENS
// ======================================================
//
// IMPORTANTE: aqui precisa colocar a URL EXATA da consulta de pesagens
// do Poseidon. Até agora você só me mostrou a consulta da área
// Conferentes.app.
//
// Assim que você me enviar a URL real da página de pesagens,
// eu ajusto aqui.
//
// Por enquanto deixamos esta URL temporária:
//
$consultaUrl = "https://poseidon.pimb.net.br/consultas/view/83?"
    . http_build_query([
        'cpf'        => POSEIDON_CPF,
        'data_inicio'=> $dataInicio,
        'data_fim'   => $dataFim,
        'navio'      => $operacao['navio'],
    ]);

// ======================================================
// 6. EXECUTA REQUISIÇÃO AUTENTICADA
// ======================================================
$html = poseidon_get($consultaUrl);

// ======================================================
// 7. MOSTRA HTML EXATAMENTE COMO VEM DO POSEIDON
// ======================================================
echo $html;
