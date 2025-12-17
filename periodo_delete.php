<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

$periodoId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($periodoId <= 0) {
    die('Período inválido.');
}

// Descobre a operação para voltar depois
$stmt = $db->prepare("SELECT operacao_id FROM periodos WHERE id = ?");
$stmt->execute([$periodoId]);
$operacaoId = $stmt->fetchColumn();

if (!$operacaoId) {
    die('Período não encontrado.');
}

$db->beginTransaction();

try {

    // IDs das funções do período
    $stmt = $db->prepare("SELECT id FROM periodo_funcoes WHERE periodo_id = ?");
    $stmt->execute([$periodoId]);
    $pfIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Remove conferentes
    if (!empty($pfIds)) {
        $in = implode(',', array_fill(0, count($pfIds), '?'));
        $stmt = $db->prepare(
            "DELETE FROM periodo_conferentes WHERE periodo_funcao_id IN ($in)"
        );
        $stmt->execute($pfIds);
    }

    // Remove funções do período
    $stmt = $db->prepare("DELETE FROM periodo_funcoes WHERE periodo_id = ?");
    $stmt->execute([$periodoId]);

    // Remove o período
    $stmt = $db->prepare("DELETE FROM periodos WHERE id = ?");
    $stmt->execute([$periodoId]);

    $db->commit();

    header("Location: /operacao_view.php?id=" . (int)$operacaoId);
    exit;

} catch (Exception $e) {
    $db->rollBack();
    echo "<pre>ERRO AO EXCLUIR PERÍODO:\n\n" . $e->getMessage() . "</pre>";
    exit;
}
