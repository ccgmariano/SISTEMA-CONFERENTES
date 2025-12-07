<?php
require_once __DIR__ . '/../../config.php';

if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit;
}

require_once "header.php";
?>

<div class="container mt-4">
    <h3>Bem-vindo, <?= $_SESSION['user'] ?></h3>
    <p>Sistema Conferentes PLUS – Primeira versão funcional.</p>
</div>

<?php require_once "footer.php"; ?>
