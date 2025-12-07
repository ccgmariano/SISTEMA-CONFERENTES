<?php 
session_start();
require_once __DIR__ . '/app/views/header.php';

// Segurança
if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit;
}
?>

<div class="card shadow-sm p-4" style="max-width: 700px; margin: auto;">
    <h3 class="mb-4 text-center">Nova Conferência</h3>

    <form method="POST" action="/app/controllers/conferencia_controller.php">

        <!-- Navio -->
        <div class="mb-3">
            <label class="form-label">Navio <span class="text-danger">*</span></label>
            <input 
                type="text" 
                class="form-control"
                name="navio"
                required
                placeholder="Ex.: DORO"
            >
        </div>

        <!-- Início -->
        <div class="mb-3">
            <label class="form-label">Data/Hora Início <span class="text-danger">*</span></label>
            <input 
                type="datetime-local" 
                class="form-control"
                name="inicio"
                required
            >
        </div>

        <!-- Fim -->
        <div class="mb-3">
            <label class="form-label">Data/Hora Fim <span class="text-danger">*</span></label>
            <input 
                type="datetime-local" 
                class="form-control"
                name="fim"
                required
            >
        </div>

        <!-- Produto -->
        <div class="mb-3">
            <label class="form-label">Produto <span class="text-danger">*</span></label>
            <select class="form-select" name="produto" required>
                <option value="">-- Selecione --</option>
                <option value="HULHA">HULHA</option>
                <option value="UREIA">UREIA</option>
                <option value="COQUE">COQUE</option>
                <option value="GRANEL">GRANEL</option>
            </select>
        </div>

        <!-- Recinto -->
        <div class="mb-3">
            <label class="form-label">Recinto <span class="text-danger">*</span></label>
            <select class="form-select" name="recinto" required>
                <option value="">-- Selecione --</option>
                <option value="EXTERNO">EXTERNO</option>
                <option value="INTERNO">INTERNO</option>
                <option value="MAXIPORT">MAXIPORT</option>
                <option value="TERMINAL 01">TERMINAL 01</option>
            </select>
        </div>

        <!-- Botão -->
        <button type="submit" class="btn btn-primary w-100">
            Continuar →
        </button>

    </form>
</div>

<?php 
require_once __DIR__ . '/app/views/footer.php';
?>
