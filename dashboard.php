<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header("Location: login.php");
    exit;
}

require_once "views/header.php";
require_once "views/menu.php";
?>

<div style="margin-left:260px; padding:20px;">
    <h2>Dashboard</h2>
    <p>Sistema operacional!</p>
</div>

<?php require_once "views/footer.php"; ?>
