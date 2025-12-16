<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';

$entidade = $_GET['entidade'] ?? null;
if (!$entidade) die('Entidade não informada');

$configPath = $_SERVER['DOCUMENT_ROOT'] . "/app/crud/{$entidade}.php";
if (!file_exists($configPath)) die('Config da entidade não encontrada');

$config = require $configPath;

$db = Database::connect();
$acao = $_GET['acao'] ?? 'list';

// LISTA
if ($acao === 'list') {
    $stmt = $db->query("SELECT * FROM {$config['tabela']} ORDER BY id DESC");
    $registros = $stmt->fetchAll();
    require $_SERVER['DOCUMENT_ROOT'] . '/app/views/crud_list.php';
    exit;
}

// FORM
if ($acao === 'form') {
    $registro = null;
    if (isset($_GET['id'])) {
        $stmt = $db->prepare("SELECT * FROM {$config['tabela']} WHERE id = :id");
        $stmt->execute(['id' => (int)$_GET['id']]);
        $registro = $stmt->fetch();
    }
    require $_SERVER['DOCUMENT_ROOT'] . '/app/views/crud_form.php';
    exit;
}

// SAVE
if ($acao === 'save') {
    $campos = array_keys($config['campos']);
    $dados = [];
    foreach ($campos as $c) {
        $dados[$c] = $_POST[$c] ?? null;
    }

    if (!empty($_POST['id'])) {
        $sets = implode(', ', array_map(fn($c) => "$c = :$c", $campos));
        $dados['id'] = $_POST['id'];
        $stmt = $db->prepare("UPDATE {$config['tabela']} SET $sets WHERE id = :id");
    } else {
        $cols = implode(',', $campos);
        $vals = implode(',', array_map(fn($c) => ":$c", $campos));
        $stmt = $db->prepare("INSERT INTO {$config['tabela']} ($cols) VALUES ($vals)");
    }

    $stmt->execute($dados);
    header("Location: /app/controllers/crud_controller.php?entidade={$entidade}");
    exit;
}
