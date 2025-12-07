<?php 
require_once __DIR__ . '/header.php';
?>

<div class="container mt-5" style="max-width: 600px;">
    <h3 class="mb-4">Nova Conferência</h3>

   <form method="POST" action="/processar_conferencia.php">

        <div class="mb-3">
            <label class="form-label">Navio</label>
            <input type="text" name="navio" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Início</label>
            <input type="datetime-local" name="inicio" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Fim</label>
            <input type="datetime-local" name="fim" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Produto</label>
            <input type="text" name="produto" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Recinto</label>
            <input type="text" name="recinto" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100 mt-3">Continuar</button>

    </form>
</div>

<?php 
require_once __DIR__ . '/footer.php';
?>
