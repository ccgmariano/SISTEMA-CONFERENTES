<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

// ID do período
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('Período inválido.');
}

// Busca período + operação (join)
$sql = "
    SELECT 
        p.id,
        p.inicio,
        p.fim,
        p.operacao_id,
        o.empresa,
        o.navio,
        o.produto,
        o.recinto,
        o.tipo_operacao
    FROM periodos p
    JOIN operacoes o ON o.id = p.operacao_id
    WHERE p.id = ?
";
$stmt = $db->prepare($sql);
$stmt->execute([$id]);
$per = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$per) {
    die('Período não encontrado.');
}

// Monta as variáveis de sessão usadas em captura.php
$_SESSION['operacao'] = [
    'empresa'       => $per['empresa'],
    'navio'         => $per['navio'],
    'produto'       => $per['produto'],
    'recinto'       => $per['recinto'],
    'tipo_operacao' => $per['tipo_operacao'],
];

$_SESSION['periodo'] = [
    'inicio' => $per['inicio'],
    'fim'    => $per['fim'],
];

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-4" style="max-width: 700px;">

    <h2>Período da Operação</h2>

    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Empresa:</strong> <?= htmlspecialchars($per['empresa']) ?></li>
        <li class="list-group-item"><strong>Tipo de Operação:</strong> <?= htmlspecialchars($per['tipo_operacao']) ?></li>
        <li class="list-group-item"><strong>Navio:</strong> <?= htmlspecialchars($per['navio']) ?></li>
        <li class="list-group-item"><strong>Produto:</strong> <?= htmlspecialchars($per['produto']) ?></li>
        <li class="list-group-item"><strong>Recinto:</strong> <?= htmlspecialchars($per['recinto']) ?></li>
        <li class="list-group-item"><strong>Início:</strong> <?= htmlspecialchars($per['inicio']) ?></li>
        <li class="list-group-item"><strong>Fim:</strong> <?= htmlspecialchars($per['fim']) ?></li>
    </ul>

    <p class="text-muted">
        Esses dados já estão na sessão e serão usados para buscar as pesagens no Poseidon.
    </p>

    <a href="/captura.php" class="btn btn-primary w-100 mb-3">
        Ir para Captura
    </a>

    <a href="/operacao_view.php?id=<?= (int)$per['operacao_id'] ?>" class="btn btn-secondary w-100">
        Voltar à Operação
    </a>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
