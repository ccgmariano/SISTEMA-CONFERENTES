<section class="auth-wrapper">
    <div class="auth-card">
        <h1>Login</h1>
        <p class="auth-subtitle">
            Use o login provisório para acessar o sistema.
        </p>

        <?php if (!empty($erro)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="/app/controllers/auth.php" class="auth-form">
            <label class="form-field">
                <span>Usuário</span>
                <input type="text" name="usuario" required autocomplete="username">
            </label>

            <label class="form-field">
                <span>Senha</span>
                <input type="password" name="senha" required autocomplete="current-password">
            </label>

            <button type="submit" class="btn btn-primary">Entrar</button>
        </form>

        <div class="hint">
            <strong>Usuário de teste:</strong> conferente<br>
            <strong>Senha:</strong> 1234
        </div>
    </div>
</section>
