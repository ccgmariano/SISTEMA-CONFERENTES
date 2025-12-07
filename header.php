<?php require_once __DIR__ . '/config.php'; ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title><?php echo APP_NAME; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Estilo básico -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body>
<header class="topbar">
    <div class="topbar-inner">

        <!-- Nome do sistema -->
        <div class="brand"><?php echo APP_NAME; ?></div>

        <!-- Navegação só aparece se estiver logado -->
        <?php if (is_logged_in()): ?>
            <nav class="topbar-nav">

                <a href="/dashboard.php">Dashboard</a>

                <a href="/nova_conferencia.php" class="btn-new">
                    Nova Conferência
                </a>

                <a href="/logout.php">Sair</a>

            </nav>
        <?php endif; ?>

    </div>
</header>

<main class="page-content">
