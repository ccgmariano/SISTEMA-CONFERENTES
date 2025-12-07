<?php
// Configuração geral do sistema

// Sempre iniciar a sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nome do sistema (pode aparecer no título da página)
define('APP_NAME', 'Sistema Conferentes PLUS');

/**
 * Verifica se o usuário está logado
 */
function is_logged_in(): bool {
    return isset($_SESSION['user']);
}

/**
 * Garante que apenas usuários logados acessem determinada página
 * Redireciona para login se não estiver logado.
 */
function require_login(): void {
    if (!is_logged_in()) {
        header('Location: /login.php');
        exit;
    }
}

/**
 * Redireciona para o dashboard se já estiver logado
 * (útil na tela de login para evitar login duplicado)
 */
function redirect_if_logged_in(): void {
    if (is_logged_in()) {
        header('Location: /dashboard.php');
        exit;
    }
}
