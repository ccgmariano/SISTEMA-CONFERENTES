<?php require_once __DIR__ . '/config.php'; ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title><?php echo APP_NAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

    <header class="topbar">
        <div class="topbar-inner">

            <div class="brand">
                <?php echo APP_NAME; ?>
            </div>

            <?php if (is_logged_in()): ?>
                <nav class="topbar-nav">

                    <!-- Dashboard -->
                    <a href="/dashboard.php">Dashboard</a>

                    <!-- NOVA CONFERÊNCIA -->
                    <a href="/app/views/nova_conferencia.php">Nova Conferência</a>

                    <!-- Logout -->
                    <a href="/logout.php">Sair</a>

                </nav>
            <?php endif; ?>

        </div>
    </header>

    <main class="page-content">
