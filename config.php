<?php
session_start();

// Caminhos importantes do projeto
define('BASE_PATH', __DIR__);
define('VIEW_PATH', BASE_PATH . '/app/views/');
define('CONTROLLER_PATH', BASE_PATH . '/app/controllers/');

// Função simples de proteção de página
function proteger() {
    if (!isset($_SESSION['user'])) {
        header("Location: /login.php");
        exit;
    }
}
