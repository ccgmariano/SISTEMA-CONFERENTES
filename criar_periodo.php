<?php
require_once __DIR__ . '/config.php';
require_login();

// Certifique-se de que exista o array de períodos
if (!isset($_SESSION['periodos'])) {
    $_SESSION['periodos'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $periodo = $_POST['periodo'] ?? null;

    if (!$periodo) {
        die("Erro: período inválido.");
    }

    // Converter o período para horários reais
    switch ($periodo) {
        case "1":
            $inicio = "07:00";
            $fim = "13:00";
            break;

        case "2":
            $inicio = "13:00";
            $fim = "19:00";
            break;

        case "3":
            $inicio = "19:00";
            $fim = "01:00";
            break;

        case "4":
            $inicio = "01:00";
            $fim = "07:00";
            break;

        default:
            die("Erro: período inválido.");
    }

    // Salvar período na sessão
    $_SESSION['periodos'][] = [
        'id'     => time(),
        'inicio' => $inicio,
        'fim'    => $fim
    ];

    header("Location: /dashboard.php");
    exit;
}

die("Requisição inválida");
