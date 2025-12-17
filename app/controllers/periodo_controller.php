<?php
require_once __DIR__ . '/../database.php';

$db = Database::connect();

// ======================================================
// VALIDAR CAMPOS RECEBIDOS
// ======================================================
$operacaoId       = isset($_POST['operacao_id']) ? (int)$_POST['operacao_id'] : 0;
$data             = $_POST['data'] ?? null;
$periodoEscolhido = $_POST['periodo_escolhido'] ?? null;
$funcoes          = $_POST['funcoes'] ?? [];
$conferentes      = $_POST['conferentes'] ?? [];

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
$inicio = $data . ' ' . $inicioHora;

if ($fimHora === '00:59' || $fimHora === '01:00' || $fimHora === '06:59') {
    $dt = new DateTime($data);
    $dt->modify('+1 day');
    $fim = $dt->format('Y-m-d') . ' ' . $fimHora;
} else {
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
// TRANSAÇÃO
// ======================================================
$db->beginTransaction();

try {

    // ==================================================
    // INSERE PERÍODO
    // ==================================================
    $stmt = $db->prepare("
        INSERT INTO periodos (operacao_id, data, inicio, fim, criado_em)
        VALUES (?, ?, ?, ?, datetime('now'))
    ");
    $stmt->execute([$operacaoId, $data, $inicio, $fim]);

    $periodoId = $db->lastInsertId();

    // ==================================================
    // FUNÇÕES DO PERÍODO
    // ==================================================
    foreach ($funcoes as $funcaoId) {

        $stmt = $db->prepare("
            INSERT INTO periodo_funcoes (periodo_id, funcao_id)
            VALUES (?, ?)
        ");
        $stmt->execute([$periodoId, $funcaoId]);

        $periodoFuncaoId = $db->lastInsertId();

        // ==================================================
        // CONFERENTES DA FUNÇÃO
        // ==================================================
        if (isset($conferentes[$funcaoId])) {
            foreach ($conferentes[$funcaoId] as $associadoId) {
                $stmt = $db->prepare("
                    INSERT INTO periodo_conferentes (periodo_funcao_id, associado_id)
                    VALUES (?, ?)
                ");
                $stmt->execute([$periodoFuncaoId, $associadoId]);
            }
        }
    }

    $db->commit();

} catch (Exception $e) {
    $db->rollBack();
    die("Erro ao criar período.");
}

// ======================================================
// REDIRECIONA
// ======================================================
header("Location: /operacao_view.php?id=" . urlencode($operacaoId));
exit;
