<?php
$pesagens = $_SESSION['pesagens'] ?? [];
?>

<div class="card mt-4">
    <div class="card-header bg-primary text-white">
        Pesagens Capturadas
    </div>

    <div class="card-body">

        <?php if (empty($pesagens)): ?>
            <p class="text-muted">Nenhuma pesagem encontrada.</p>

        <?php else: ?>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Hora</th>
                        <th>Peso (kg)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pesagens as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['hora']) ?></td>
                            <td><?= number_format($p['peso'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="alert alert-success mt-3">
                ✔ Dados simulados carregados!  
                Em breve conectaremos ao Poseidon.
            </div>
        <?php endif; ?>

    </div>
</div>
