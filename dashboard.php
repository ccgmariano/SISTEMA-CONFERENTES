<?php
require_once __DIR__ . '/config.php';

// Garante que apenas usuários logados acessem
require_login();

include __DIR__ . '/app/views/header.php';
include __DIR__ . '/app/views/dashboard.php';
include __DIR__ . '/app/views/footer.php';
