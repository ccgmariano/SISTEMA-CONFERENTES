<?php 
require_once __DIR__ . '/header.php';
?>

<div class="container mt-5" style="max-width: 600px;">
    <h3 class="mb-4">Confirmar Conferência</h3>

    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Navio:</strong> <?= htmlspecialchars($navio) ?></li>
        <li class="list-group-item"><strong>Início:</strong> <?= htmlspecialchars($inicio) ?></li>
        <li class="list-group-item"><strong>Fim:</strong> <?= htmlspecialchars($fim) ?></li>
        <li class="list-group-item"><strong>Produto:</strong> <?= htmlspecialchars($produto) ?></li>
        <li class="list-group-item"><strong>Recinto:</strong> <?= htmlspecialchars($recinto) ?></li>
    </ul>

    <p>Em seguida implementaremos a consulta automática no Poseidon com esses dados.</p>

    <a class="btn btn-secondary w-100" href="/app/views/nova_conferencia.php">Voltar</a>
</div>

<?php 
require_once __DIR__ . '/footer.php';
?>
