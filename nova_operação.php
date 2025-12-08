<?php
session_start();
require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-5" style="max-width: 600px;">
    <h3 class="mb-4">Nova Operação</h3>

    <p class="mb-3">
        Preencha os dados principais da operação.  
        Esses dados serão usados depois para buscar as pesagens automaticamente,
        sem precisar digitar tudo de novo.
    </p>

    <form method="POST" action="/app/controllers/operacao_controller.php">
        <label class="form-label">Cliente / Empresa:</label>
        <input class="form-control mb-3" type="text" name="empresa" required>

        <label class="form-label">Navio:</label>
        <input class="form-control mb-3" type="text" name="navio" required>

        <label class="form-label">Produto:</label>
        <input class="form-control mb-3" type="text" name="produto" required>

        <label class="form-label">Recinto:</label>
        <input class="form-control mb-3" type="text" name="recinto" required>

        <label class="form-label">Tipo de Operação:</label>
        <select class="form-control mb-4" name="tipo_operacao" required>
            <option value="">Selecione...</option>
            <option value="carga">Carga</option>
            <option value="descarga">Descarga</option>
        </select>

        <button class="btn btn-primary w-100" type="submit">Salvar operação</button>
    </form>
</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
