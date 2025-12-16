<?php 
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
?>
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

        <div class="brand"><?php echo APP_NAME; ?></div>

        <?php if (is_logged_in()): ?>
            <nav class="topbar-nav">
                <a href="/dashboard.php">Dashboard</a>
                <a href="/nova_operacao.php">Nova Operação</a>

                <div class="dropdown">
                    <span>Cadastros</span>
                    <div class="dropdown-content">
                        <a href="/app/controllers/associados_controller.php">Associados</a>
                        <a href="/app/controllers/crud_controller.php?entidade=navios">Navios</a>
                        <a href="/app/controllers/crud_controller.php?entidade=cargas">Cargas</a>
                        <a href="/app/controllers/crud_controller.php?entidade=equipamentos">Equipamentos</a>
                        <a href="/app/controllers/crud_controller.php?entidade=decks">Decks</a>
                        <a href="/app/controllers/crud_controller.php?entidade=origem_destino">Origem/Destino</a>
                        <a href="/app/controllers/crud_controller.php?entidade=balancas">Balanças</a>
                        <a href="/app/controllers/crud_controller.php?entidade=operadores">Operadores Portuários</a>
                        <a href="/app/controllers/crud_controller.php?entidade=funcoes">Funções</a>
                        <a href="/app/controllers/crud_controller.php?entidade=paralisacoes">Paralisações</a>
                    </div>
                </div>

                <a href="/logout.php">Sair</a>
            </nav>
        <?php endif; ?>

    </div>
</header>

<main class="page-content">
