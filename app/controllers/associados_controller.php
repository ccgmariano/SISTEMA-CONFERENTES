<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';

$db = Database::connect();

$acao = $_GET['acao'] ?? 'list';

// LISTAGEM
if ($acao === 'list') {
    $stmt = $db->query("
        SELECT id, nome, codigo, ativo
        FROM associados
        ORDER BY nome
    ");
    $associados = $stmt->fetchAll();
    require $_SERVER['DOCUMENT_ROOT'] . '/app/views/associados_list.php';
    exit;
}

// FORMULÁRIO
if ($acao === 'form') {
    $assoc = null;

    if (isset($_GET['id'])) {
        $stmt = $db->prepare("SELECT * FROM associados WHERE id = :id");
        $stmt->execute(['id' => (int)$_GET['id']]);
        $assoc = $stmt->fetch();
    }

    require $_SERVER['DOCUMENT_ROOT'] . '/app/views/associados_form.php';
    exit;
}

// SALVAR
if ($acao === 'save') {
    $id   = $_POST['id'] ?? null;
    $nome = trim($_POST['nome']);
    $codigo = trim($_POST['codigo'] ?? '');
    $obs  = trim($_POST['observacoes'] ?? '');

    if ($id) {
        $stmt = $db->prepare("
            UPDATE associados
               SET nome = :nome,
                   codigo = :codigo,
                   observacoes = :obs
             WHERE id = :id
        ");
        $stmt->execute(compact('nome','codigo','obs','id'));
    } else {
        $stmt = $db->prepare("
            INSERT INTO associados (nome, codigo, observacoes)
            VALUES (:nome, :codigo, :obs)
        ");
        $stmt->execute(compact('nome','codigo','obs'));
    }

    header('Location: /app/controllers/associados_controller.php');
    exit;
}

// ATIVAR / DESATIVAR
if ($acao === 'toggle') {
    $id = (int)$_GET['id'];
    $db->exec("
        UPDATE associados
           SET ativo = CASE ativo WHEN 1 THEN 0 ELSE 1 END
         WHERE id = $id
    ");
    header('Location: /app/controllers/associados_controller.php');
    exit;
}
