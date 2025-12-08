<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
?>
<header class="topbar">
    <div class="topbar-inner">
        <div class="brand"><?php echo APP_NAME; ?></div>

        <?php if (is_logged_in()): ?>
            <nav class="topbar-nav">
                <a href="/dashboard.php">Dashboard</a>
                <a href="/nova_operacao.php">Nova Operação</a>
                <a href="/logout.php">Sair</a>
            </nav>
        <?php endif; ?>
    </div>
</header>
