<?php
require_once __DIR__ . '/../database.php';

$db = Database::connect();

$periodoId        = isset($_POST['periodo_id']) ? (int)$_POST['periodo_id'] : 0;
$operacaoId       = isset($_POST['operacao_id']) ? (int)$_POST['operacao_id'] : 0;
$data             = $_POST['data'] ?? null;
$periodoEscolhido = $_POST['periodo_escolhido'] ?? null;
$funcoes          = $_POST['funcoes'] ?? [];
$conferentes      = $_POST['conferentes'] ?? [];

if ($periodoId <= 0 || $operacaoId <= 0 || !$data || !$periodoEscolhido) {
    die("Erro: dados do período incompletos.");
}

list($inicioHora, $fimHora) = explode('|', $periodoEscolhido);

$inicio = $data . ' ' . $inicioHora;

if ($fimHora === '00:59' || $fimHora === '01:00' || $fimHora === '06:59') {
    $dt = new DateTime($data);
    $dt->modify('+1 day');
    $fim = $dt->format('Y-m-d') . ' ' . $fimHora;
} else {
    $fim = $data . ' ' . $fimHora;
}

$db->beginTransaction();

try {

    // Atualiza o período
    $stmt = $db->prepare("
        UPDATE periodos
        SET data = ?, inicio = ?, fim = ?
        WHERE id = ? AND operacao_id = ?
    ");
    $stmt->execute([$data, $inicio, $fim, $periodoId, $operacaoId]);

    // Apaga vínculos atuais (conferentes -> funcoes)
    $stmt = $db->prepare("SELECT id FROM periodo_funcoes WHERE periodo_id = ?");
    $stmt->execute([$periodoId]);
    $pfIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($pfIds)) {
        $in = implode(',', array_fill(0, count($pfIds), '?'));
        $stmt = $db->prepare("DELETE FROM periodo_conferentes WHERE periodo_funcao_id IN ($in)");
        $stmt->execute($pfIds);
    }

    $stmt = $db->prepare("DELETE FROM periodo_funcoes WHERE periodo_id = ?");
    $stmt->execute([$periodoId]);

    // Recria vínculos
    foreach ($funcoes as $funcaoId) {
        $funcaoId = (int)$funcaoId;

        $stmt = $db->prepare("
            INSERT INTO periodo_funcoes (periodo_id, funcao_id)
            VALUES (?, ?)
        ");
        $stmt->execute([$periodoId, $funcaoId]);

        $periodoFuncaoId = (int)$db->lastInsertId();

        if (isset($conferentes[$funcaoId])) {
            foreach ($conferentes[$funcaoId] as $associadoId) {
                $associadoId = (int)$associadoId;

                $stmt = $db->prepare("
                    INSERT INTO periodo_conferentes (periodo_funcao_id, associado_id)
                    VALUES (?, ?)
                ");
                $stmt->execute([$periodoFuncaoId, $associadoId]);
            }
        }
    }

    $db->commit();

    header("Location: /periodo_view.php?id=" . $periodoId);
    exit;

} catch (Exception $e) {
    $db->rollBack();
    echo "<pre>ERRO AO ATUALIZAR PERÍODO:\n\n" . $e->getMessage() . "</pre>";
    exit;
}
