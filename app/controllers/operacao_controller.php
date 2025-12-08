<?php
session_start();

// Validação básica
$empresa       = $_POST['empresa']       ?? null;
$navio         = $_POST['navio']         ?? null;
$produto       = $_POST['produto']       ?? null;
$recinto       = $_POST['recinto']       ?? null;
$tipoOperacao  = $_POST['tipo_operacao'] ?? null;

if (!$empresa || !$navio || !$produto || !$recinto || !$tipoOperacao) {
    die("Erro: todos os campos são obrigatórios.");
}

// Salva a operação na SESSÃO usando a chave correta:
$_SESSION['operacao'] = [
    'empresa'       => $empresa,
    'navio'         => $navio,
    'produto'       => $produto,
    'recinto'       => $recinto,
    'tipo'          => $tipoOperacao,
    'criado_em'     => date('Y-m-d H:i:s'),
];

// Redireciona para o dashboard
header("Location: /dashboard.php");
exit;
