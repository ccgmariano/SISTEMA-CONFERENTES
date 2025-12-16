<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';

/* ======================================================
   CONFIGURAÇÃO INICIAL
====================================================== */

$entidade = $_GET['entidade'] ?? null;
if (!$entidade) {
    die('Entidade não informada');
}

$configPath = $_SERVER['DOCUMENT_ROOT'] . "/app/crud/{$entidade}.php";
if (!file_exists($configPath)) {
    die('Config da entidade não encontrada');
}

$config = require $configPath;
$db = Database::connect();

$acao = $_GET['acao'] ?? 'list';
$operadorId = isset($_GET['operador_id']) ? (int)$_GET['operador_id'] : null;

/* ======================================================
   LISTAGEM
====================================================== */

if ($acao === 'list') {

    $sql = "SELECT * FROM {$config['tabela']}";

    if ($entidade === 'contatos_operadores' && $operadorId) {
        $sql .= " WHERE operador_id = :operador_id";
    }

    $sql .= " ORDER BY id DESC";

    $stmt = $db->prepare($sql);

    if ($entidade === 'contatos_operadores' && $operadorId) {
        $stmt->execute(['operador_id' => $operadorId]);
    } else {
        $stmt->execute();
    }

    $registros = $stmt->fetchAll(PDO::FETCH_ASSOC);

    require $_SERVER['DOCUMENT_ROOT'] . '/app/views/crud_list.php';
    exit;
}

/* ======================================================
   FORMULÁRIO
====================================================== */

if ($acao === 'form') {

    $registro = null;

    if (!empty($_GET['id'])) {
        $stmt = $db->prepare("SELECT * FROM {$config['tabela']} WHERE id = :id");
        $stmt->execute(['id' => (int)$_GET['id']]);
        $registro = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    require $_SERVER['DOCUMENT_ROOT'] . '/app/views/crud_form.php';
    exit;
}

/* ======================================================
   SALVAR (INSERT / UPDATE)
====================================================== */

if ($acao === 'save') {

    /* força vínculo com operador */
    if ($entidade === 'contatos_operadores' && $operadorId) {
        $_POST['operador_id'] = $operadorId;
    }

    $campos = array_keys($config['campos']);
    $dados = [];

    foreach ($campos as $campo) {
        $dados[$campo] = $_POST[$campo] ?? null;
    }

    if (!empty($_POST['id'])) {
        // UPDATE
        $sets = [];
        foreach ($campos as $campo) {
            $sets[] = "{$campo} = :{$campo}";
        }

        $dados['id'] = (int)$_POST['id'];

        $sql = "UPDATE {$config['tabela']} SET " . implode(', ', $sets) . " WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->execute($dados);

    } else {
        // INSERT
        $cols = implode(', ', $campos);
        $vals = ':' . implode(', :', $campos);

        $sql = "INSERT INTO {$config['tabela']} ({$cols}) VALUES ({$vals})";
        $stmt = $db->prepare($sql);
        $stmt->execute($dados);
    }

    /* redirecionamento correto */
    if ($entidade === 'contatos_operadores' && $operadorId) {
        header("Location: /app/controllers/crud_controller.php?entidade=contatos_operadores&operador_id={$operadorId}");
    } else {
        header("Location: /app/controllers/crud_controller.php?entidade={$entidade}");
    }

    exit;
}
