<?php
require_once __DIR__ . '/config.php';

if (!is_logged_in()) {
    header("Location: /login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /nova_conferencia.php");
    exit;
}

$navio   = trim($_POST['navio'] ?? '');
$inicio  = trim($_POST['inicio'] ?? '');
$fim     = trim($_POST['fim'] ?? '');
$produto = trim($_POST['produto'] ?? '');
$recinto = trim($_POST['recinto'] ?? '');

$erros = [];

if ($navio === '')   $erros[] = "Informe o navio.";
if ($inicio === '')  $erros[] = "Informe a data/hora de início.";
if ($fim === '')     $erros[] = "Informe a data/hora de fim.";
if ($produto === '') $erros[] = "Informe o produto.";
if ($recinto === '') $erros[] = "Informe o recinto.";

include __DIR__ . '/app/views/header.php';

echo "<h2>Revisão da Conferência</h2>";

if ($erros) {
    echo "<div class='erro'><ul>";
    foreach ($erros as $e) {
        echo "<li>$e</li>";
    }
    echo "</ul></div>";

    echo "<p><a href='/nova_conferencia.php'>Voltar</a></p>";
} else {
    echo "<p><strong>Navio:</strong> $navio</p>";
    echo "<p><strong>Início:</strong> $inicio</p>";
    echo "<p><strong>Fim:</strong> $fim</p>";
    echo "<p><strong>Produto:</strong> $produto</p>";
    echo "<p><strong>Recinto:</strong> $recinto</p>";

    echo "<br><p>✔ Dados recebidos! (Em breve salvaremos no banco ou geraremos arquivo)</p>";
}

include __DIR__ . '/app/views/footer.php';
