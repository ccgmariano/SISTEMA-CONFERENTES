<?php require_once __DIR__ . '/../../config.php'; ?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title><?php echo APP_NAME; ?></title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <!-- Ícones (opcional e leve) -->
    <link 
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css"
        rel="stylesheet"
    >

    <!-- Seu CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body class="bg-light">

<header class="bg-dark text-white py-2 mb-4 shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        
        <div class="fw-bold fs-5">
            <?php echo APP_NAME; ?>
        </div>

        <?php if (is_logged_in()): ?>
            <nav class="d-flex gap-3">
                <a class="text-white text-decoration-none" href="/dashboard.php">Dashboard</a>
                <a class="text-white text-decoration-none" href="/app/views/nova_conferencia.php">Nova Conferência</a>
                <a class="text-white text-decoration-none" href="/logout.php">Sair</a>
            </nav>
        <?php endif; ?>
    </div>
</header>

<main class="container py-3">
