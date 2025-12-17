<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';
$db = Database::connect();

// ===============================
// VALIDAR DADOS BÁSICOS
// ===============================
$periodo_id      = (int)($_POST['periodo_id'] ?? 0);
$ticket          = trim($_POST['ticket'] ?? '');
$data_hora       = trim($_POST['data_hora'] ?? '');
$peso_liquido    = trim($_POST['peso_liquido'] ?? '');

if ($periodo_id <= 0 || !$ticket || !$data_hora || !$peso_liquido) {
    http_response_code(400);
    echo "Dados obrigatórios ausentes.";
    exit;
}

// ===============================
// INSERIR PESAGEM
// ===============================
try {
    $stmt = $db->prepare("
        INSERT INTO pesagens (
            periodo_id,
            ticket,
            placa,
            empresa,
            data_hora,
            peso_liquido,
            carga,
            operacao,
            terno,
            equipamento,
            porao,
            deck,
            origem_destino
        ) VALUES (
            :periodo_id,
            :ticket,
            :placa,
            :empresa,
            :data_hora,
            :peso_liquido,
            :carga,
            :operacao,
            :terno,
            :equipamento,
            :porao,
            :deck,
            :origem_destino
        )
    ");

    $stmt->execute([
        ':periodo_id'     => $periodo_id,
        ':ticket'         => $ticket,
        ':placa'          => $_POST['placa'] ?? null,
        ':empresa'        => $_POST['empresa'] ?? null,
        ':data_hora'      => $data_hora,
        ':peso_liquido'   => $peso_liquido,
        ':carga'          => $_POST['carga'] ?? null,
        ':operacao'       => $_POST['operacao'] ?? null,
        ':terno'          => $_POST['terno'] ?? null,
        ':equipamento'    => $_POST['equipamento'] ?? null,
        ':porao'          => $_POST['porao'] ?? null,
        ':deck'           => $_POST['deck'] ?? null,
        ':origem_destino' => $_POST['origem_destino'] ?? null,
    ]);

    echo "OK";

} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        http_response_code(409);
        echo "Pesagem já conferida.";
    } else {
        http_response_code(500);
        echo "Erro ao salvar pesagem.";
    }
}
