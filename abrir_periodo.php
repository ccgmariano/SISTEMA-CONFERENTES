<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: /dashboard.php');
    exit;
}

$sql = "
    SELECT 
        p.id,
        p.inicio,
        p.fim,
        p.operacao_id,
        o.empresa,
        o.navio,
        o.produto,
        o.recinto,
        o.tipo_operacao
    FROM periodos p
    JOIN operacoes o ON o.id = p.operacao_id
    WHERE p.id = ?
";
$stmt = $db->prepare($sql);
$stmt->execute([$id]);
$per = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$per) {
    header('Location: /dashboard.php');
    exit;
}

// Prepara sessão igual ao periodo_view.php
$_SESSION['operacao'] = [
    'empresa'       => $per['empresa'],
    'navio'         => $per['navio'],
    'produto'       => $per['produto'],
    'recinto'       => $per['recinto'],
    'tipo_operacao' => $per['tipo_operacao'],
];

$_SESSION['periodo'] = [
    'inicio' => $per['inicio'],
    'fim'    => $per['fim'],
];

// Vai direto para captura
header('Location: /captura.php');
exit;
