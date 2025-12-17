<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';
$db = Database::connect();

$periodo_id = isset($_GET['periodo_id']) ? (int)$_GET['periodo_id'] : 0;
if ($periodo_id <= 0) {
    die('Período inválido.');
}

$stmt = $db->prepare("
    SELECT
        id,
        ticket,
        placa,
        peso_liquido,
        data_hora,
        porao,
        deck,
        equipamento
    FROM pesagens
    WHERE periodo_id = ?
    ORDER BY data_hora
");
$stmt->execute([$periodo_id]);
$pesagens = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($pesagens)) {
    echo "<p><em>Nenhuma pesagem conferida ainda.</em></p>";
    exit;
}
?>

<form id="formPesagens">

<div style="margin-bottom:10px;">
    <button type="button"
            id="btnExcluir"
            disabled
            onclick="excluirSelecionadas()">
        Excluir selecionadas
    </button>
</div>

<table border="1" cellpadding="6" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>
                <input type="checkbox" id="checkAll">
            </th>
            <th>Ticket</th>
            <th>Placa</th>
            <th>Peso Líquido</th>
            <th>Data/Hora</th>
            <th>Porão</th>
            <th>Deck</th>
            <th>Equipamento</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($pesagens as $p): ?>
            <tr>
                <td>
                    <input type="checkbox"
                           name="pesagem_ids[]"
                           value="<?= (int)$p['id'] ?>"
                           onchange="atualizarBotao()">
                </td>
                <td><?= htmlspecialchars($p['ticket']) ?></td>
                <td><?= htmlspecialchars($p['placa']) ?></td>
                <td><?= htmlspecialchars($p['peso_liquido']) ?></td>
                <td><?= htmlspecialchars($p['data_hora']) ?></td>
                <td><?= htmlspecialchars($p['porao']) ?></td>
                <td><?= htmlspecialchars($p['deck']) ?></td>
                <td><?= htmlspecialchars($p['equipamento']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</form>

<script>
function atualizarBotao() {
    const checks = document.querySelectorAll('input[name="pesagem_ids[]"]:checked');
    document.getElementById('btnExcluir').disabled = (checks.length === 0);
}

document.getElementById('checkAll').addEventListener('change', function () {
    const checks = document.querySelectorAll('input[name="pesagem_ids[]"]');
    checks.forEach(c => c.checked = this.checked);
    atualizarBotao();
});

function excluirSelecionadas() {
    if (!confirm('Confirma excluir as pesagens selecionadas?')) {
        return;
    }

    const form = document.getElementById('formPesagens');
    const formData = new FormData(form);

    fetch('/app/controllers/pesagem_delete_batch.php', {
        method: 'POST',
        body: formData
    })
    .then(r => r.text())
    .then(resp => {
        if (resp.trim() === 'OK') {
            location.reload();
        } else {
            alert(resp);
        }
    })
    .catch(() => {
        alert('Erro ao excluir pesagens.');
    });
}
</script>
