<?php
session_start();

// Garantir que operação e período existem
if (!isset($_SESSION['operacao']) || !isset($_SESSION['periodo'])) {
    echo "<p>Erro: sessão expirada.</p>";
    exit;
}

$op  = $_SESSION['operacao'];
$per = $_SESSION['periodo'];

// Aqui montamos a URL (igual conferentes faz com teste.php)
$url = "https://conferentes.app.br/teste.php?"
     . "navio="   . urlencode($op['navio'])
     . "&inicio=" . urlencode($per['inicio'])
     . "&termino=". urlencode($per['fim'])
     . "&produto=". urlencode($op['produto'])
     . "&recinto=". urlencode($op['recinto']);

// --- FASE 1: SIMULAÇÃO ---
// Enquanto não conectamos ao Poseidon:
$dadosSimulados = [
    ["PLACA001", "07:10", "36.120", "Vazio"],
    ["PLACA002", "07:12", "48.310", "Carregado"],
    ["PLACA003", "07:15", "52.900", "Carregado"],
];

// Renderiza a tabela
ob_start();
include __DIR__ . "/../views/partials/tabela_pesagens.php";
$html = ob_get_clean();

echo $html;
