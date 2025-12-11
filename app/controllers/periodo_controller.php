<?php
require_once __DIR__ . '/../database.php';

$db = Database::connect();

// ======================================================
// VALIDAR CAMPOS RECEBIDOS
// ======================================================
$operacaoId        = isset($_POST['operacao_id']) ? (int)$_POST['operacao_id'] : 0;
$data              = $_POST['data'] ?? null;
$periodoEscolhido  = $_POST['periodo_escolhido'] ?? null;

if ($operacaoId <= 0 || !$data || !$periodoEscolhido) {
    die("Erro: dados do período incompletos.");
}

// ======================================================
// EXTRAI HORÁRIOS SELECIONADOS
// ======================================================
list($inicioHora, $fimHora) = explode('|', $periodoEscolhido);

// ======================================================
// GERA DATETIME COMPLETO
// ======================================================
// data vem como YYYY-MM-DD — mantemos esse formato

$inicio = $data . ' ' . $inicioHora;

// Caso o período atravesse a meia-noite (Ex: 19:00 → 00:59)
if ($fimHora === '00:59' || $fimHora === '01:00' || $fimHora === '06:59') {

    // Determinar se precisa somar 1 dia
    // períodos oficiais:
    //  - 19:00 → 00:59  --> vira dia seguinte
    //  - 01:00 → 06:59  --> também pertence ao dia seguinte
    $dt = new DateTime($data);
    $dt->modify('+1 day');
    $dataFinal = $dt->format('Y-m-d');

    $fim = $dataFinal . ' ' . $fimHora;

} else {
    // períodos normais dentro do mesmo dia
    $fim = $data . ' ' . $fimHora;
}

// ======================================================
// EVITAR DUPLICAÇÃO DO MESMO PERÍODO
// ======================================================
$stmt = $db->prepare("
    SELECT COUNT(*) FROM periodos
    WHERE operacao_id = ?
      AND inicio = ?
      AND fim = ?
      AND data = ?
");
$stmt->execute([$operacaoId, $inicio, $fim, $data]);

if ($stmt->fetchColumn() > 0) {
    header('Location: /operacao_view.php?id=' . urlencode($operacaoId));
    exit;
}

// ======================================================
// INSERE NO BANCO
// ======================================================
$stmt = $db->prepare("
    INSERT INTO periodos (operacao_id, data, inicio, fim, criado_em)
    VALUES (?, ?, ?, ?, datetime('now'))
");
$stmt->execute([$operacaoId, $data, $inicio, $fim]);

// ======================================================
// REDIRECIONA DE VOLTA PARA A OPERAÇÃO
// ======================================================
header("Location: /operacao_view.php?id=" . urlencode($operacaoId));
exit;
