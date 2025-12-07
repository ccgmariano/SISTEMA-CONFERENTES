<?php
require_once "views/header.php";
?>

<div class="container mt-5" style="max-width: 400px;">
    <h3 class="text-center">Sistema Conferentes PLUS</h3>

    <form method="post" action="controllers/auth.php">
        <div class="mb-3">
            <label>Usuário</label>
            <input type="text" name="usuario" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Senha</label>
            <input type="password" name="senha" class="form-control" required>
        </div>

        <button class="btn btn-primary w-100">Entrar</button>
    </form>
</div>

<?php
require_once "views/footer.php";
?>
