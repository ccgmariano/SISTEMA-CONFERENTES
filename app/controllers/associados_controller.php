<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';

$db = Database::connect();

$acao = $_GET['acao'] ?? 'list';

// =============================
// LISTAGEM
// =============================
if ($acao === 'list') {
    $stmt = $db->query("
        SELECT id, nome, cpf, codigo, ativo
        FROM associados
        ORDER BY nome
    ");
    $associados = $stmt->fetchAll();
    require $_SERVER['DOCUMENT_ROOT'] . '/app/views/associados_list.php';
    exit;
}

// =============================
// FORMULÁRIO
// =============================
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

// =============================
// SALVAR (INSERT / UPDATE)
// =============================
if ($acao === 'save') {

    $id     = $_POST['id'] ?? null;
    $nome   = trim($_POST['nome']);
    $cpf    = trim($_POST['cpf'] ?? '');
    $senha  = $_POST['senha'] ?? '';
    $codigo = trim($_POST['codigo'] ?? '');
    $obs    = trim($_POST['observacoes'] ?? '');

    // INSERT
    if (!$id) {

        $senhaHash = $senha ? password_hash($senha, PASSWORD_DEFAULT) : null;

        $stmt = $db->prepare("
            INSERT INTO associados (nome, cpf, senha, codigo, observacoes)
            VALUES (:nome, :cpf, :senha, :codigo, :obs)
        ");
        $stmt->execute([
            'nome'   => $nome,
            'cpf'    => $cpf,
            'senha'  => $senhaHash,
            'codigo' => $codigo,
            'obs'    => $obs
        ]);

    } 
    // UPDATE
    else {

        if ($senha) {
            // Atualiza com senha
            $stmt = $db->prepare("
                UPDATE associados
                   SET nome = :nome,
                       cpf = :cpf,
                       senha = :senha,
                       codigo = :codigo,
                       observacoes = :obs
                 WHERE id = :id
            ");
            $stmt->execute([
                'nome'   => $nome,
                'cpf'    => $cpf,
                'senha'  => password_hash($senha, PASSWORD_DEFAULT),
                'codigo' => $codigo,
                'obs'    => $obs,
                'id'     => $id
            ]);
        } else {
            // Atualiza sem mexer na senha
            $stmt = $db->prepare("
                UPDATE associados
                   SET nome = :nome,
                       cpf = :cpf,
                       codigo = :codigo,
                       observacoes = :obs
                 WHERE id = :id
            ");
            $stmt->execute([
                'nome'   => $nome,
                'cpf'    => $cpf,
                'codigo' => $codigo,
                'obs'    => $obs,
                'id'     => $id
            ]);
        }
    }

    header('Location: /app/controllers/associados_controller.php');
    exit;
}

// =============================
// ATIVAR / DESATIVAR
// =============================
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
