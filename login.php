<?php
require_once __DIR__ . '/config.php';

// Se já estiver logado, manda direto para o dashboard
redirect_if_logged_in();

$erro = $_SESSION['login_error'] ?? null;
unset($_SESSION['login_error']);

include __DIR__ . '/app/views/header.php';
include __DIR__ . '/app/views/login.php';
include __DIR__ . '/app/views/footer.php';
