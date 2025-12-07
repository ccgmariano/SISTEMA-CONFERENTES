<?php
session_start();
require_once __DIR__ . '/config.php';

if (!is_logged_in()) {
    header("Location: /login.php");
    exit;
}

include __DIR__ . '/app/views/header.php';
include __DIR__ . '/app/views/nova_conferencia.php';
include __DIR__ . '/app/views/footer.php';
