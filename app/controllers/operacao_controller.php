<?php
session_start();

// Validação básica
$empresa      = $_POST['empresa']      ?? null;
$navio        = $_POST['navio']        ?? null;
$produto      = $_POST['produto']      ?? null;
$recinto      = $_POST['recinto']      ?? null;
$tipoOperacao = $_POST['tipo_operacao'] ?? null;

if (!$empresa || !$navio || !$produto || !$recinto || !$tipoOperacao) {
    die("Erro: todos os campos são obrigatórios.");
}

// Aqui estamos só guardando em sessão.
// Mais tarde isso vira tabela de banco “operacoes”.
$_SESSION['operacao_atual'] = [
    'empresa'       => $empresa,
    'navio'         => $navio,
    'produto'       => $produto,
    'recinto'       => $recinto,
    'tipo_operacao' => $tipoOperacao,
    'criado_em'     => date('Y-m-d H:i:s'),
];

// Volta para o dashboard
header("Location: /dashboard.php");
exit;
