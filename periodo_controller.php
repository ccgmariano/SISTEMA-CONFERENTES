<?php
session_start();

// Precisa ter operação ativa
if (!isset($_SESSION['operacao'])) {
    header('Location: /dashboard.php');
    exit;
}

$acao  = $_POST['acao'] ?? 'criar';
$inicio = $_POST['inicio'] ?? null;
$fim    = $_POST['fim'] ?? null;

// Garante array de períodos na sessão
if (!isset($_SESSION['periodos']) || !is_array($_SESSION['periodos'])) {
    $_SESSION['periodos'] = [];
}

switch ($acao) {

    // -------------------------------------------------
    // 1) CRIAR NOVO PERÍODO (não apaga os antigos)
    // -------------------------------------------------
    case 'criar':

        if (!$inicio || !$fim) {
            die('Erro: período inválido.');
        }

        // Verifica se esse período já existe
        $jaExiste = false;
        foreach ($_SESSION['periodos'] as $p) {
            if ($p['inicio'] === $inicio && $p['fim'] === $fim) {
                $jaExiste = true;
                break;
            }
        }

        // Se não existir, adiciona
        if (!$jaExiste) {
            $_SESSION['periodos'][] = [
                'inicio'    => $inicio,
                'fim'       => $fim,
                'criado_em' => date('Y-m-d H:i:s'),
            ];
        }

        // Período “selecionado” (usado pela captura.php atual)
        $_SESSION['periodo'] = [
            'inicio' => $inicio,
            'fim'    => $fim,
        ];

        header('Location: /dashboard.php');
        exit;

    // -------------------------------------------------
    // 2) SELECIONAR UM PERÍODO EXISTENTE PARA CAPTURAR
    // -------------------------------------------------
    case 'selecionar':

        $index = isset($_POST['index']) ? (int)$_POST['index'] : -1;

        if ($index < 0 || !isset($_SESSION['periodos'][$index])) {
            die('Erro: período não encontrado.');
        }

        $per = $_SESSION['periodos'][$index];

        $_SESSION['periodo'] = [
            'inicio' => $per['inicio'],
            'fim'    => $per['fim'],
        ];

        header('Location: /captura.php');
        exit;

    // -------------------------------------------------
    // 3) EXCLUIR PERÍODO
    // -------------------------------------------------
    case 'excluir':

        $index = isset($_POST['index']) ? (int)$_POST['index'] : -1;

        if ($index >= 0 && isset($_SESSION['periodos'][$index])) {
            $removido = $_SESSION['periodos'][$index];
            unset($_SESSION['periodos'][$index]);
            $_SESSION['periodos'] = array_values($_SESSION['periodos']); // reindexa

            // Se o período removido era o selecionado, limpa seleção
            if (isset($_SESSION['periodo'])) {
                $sel = $_SESSION['periodo'];
                if ($sel['inicio'] === $removido['inicio'] && $sel['fim'] === $removido['fim']) {
                    unset($_SESSION['periodo']);
                }
            }
        }

        header('Location: /dashboard.php');
        exit;

    default:
        die('Ação de período inválida.');
}
