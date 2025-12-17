<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

// ======================================================
// 1. VALIDAR ENTRADAS
// ======================================================
$periodoId = isset($_GET['periodo_id']) ? (int)$_GET['periodo_id'] : 0;
$ticket    = isset($_GET['ticket']) ? (int)$_GET['ticket'] : 0;

if ($periodoId <= 0 || $ticket <= 0) {
    die('Parâmetros inválidos.');
}

// ======================================================
// 2. BUSCAR PERÍODO + OPERAÇÃO
// ======================================================
$stmt = $db->prepare("
    SELECT 
        p.inicio,
        p.fim,
        o.navio
    FROM periodos p
    JOIN operacoes o ON o.id = p.operacao_id
    WHERE p.id = ?
");
$stmt->execute([$periodoId]);
$periodo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    die('Período não encontrado.');
}

// ======================================================
// 3. CONSULTAR POSEIDON (1 TICKET)
// ======================================================
function dataBR($iso) {
    $dt = DateTime::createFromFormat('Y-m-d H:i', $iso);
    return $dt ? $dt->format('d/m/Y H:i') : '';
}

$payload = json_encode([
    'data_inicio' => dataBR($periodo['inicio']),
    'data_fim'    => dataBR($periodo['fim']),
    'navio'       => trim($periodo['navio']),
    'ticket'      => $ticket
]);

$ch = curl_init("https://sistema-conferentes-node.onrender.com/poseidon/pesagens");
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $payload,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json']
]);

$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (!$data || empty($data['registros'])) {
    die('Pesagem não encontrada no Poseidon.');
}

$r = $data['registros'][0];

// ======================================================
// 4. FORMULÁRIO
// ======================================================
require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-4">
    <h3>Conferência de Pesagem</h3>

    <form method="POST" action="/app/controllers/pesagem_salvar.php">

        <input type="hidden" name="periodo_id" value="<?= $periodoId ?>">
        <input type="hidden" name="ticket" value="<?= htmlspecialchars($r['ticket_id']) ?>">

        <h5>Dados da Pesagem</h5>

        <div class="row">
            <div class="col">
                <label>Ticket</label>
                <input class="form-control" value="<?= $r['ticket_id'] ?>" disabled>
            </div>
            <div class="col">
                <label>Placa</label>
                <input class="form-control" value="<?= $r['placa'] ?>" disabled>
            </div>
            <div class="col">
                <label>Peso Líquido</label>
                <input class="form-control" value="<?= $r['peso_liquido'] ?>" disabled>
            </div>
        </div>

        <hr>

        <h5>Dados Operacionais</h5>

        <div class="row">
            <div class="col">
                <label>Porão</label>
                <input name="porao" class="form-control" required>
            </div>
            <div class="col">
                <label>Deck</label>
                <input name="deck" class="form-control" required>
            </div>
            <div class="col">
                <label>Equipamento</label>
                <input name="equipamento" class="form-control" required>
            </div>
        </div>

        <div class="row mt-2">
            <div class="col">
                <label>Origem/Destino</label>
                <input name="origem_destino" class="form-control" required>
            </div>
        </div>

        <hr>

        <a href="/periodo_view.php?id=<?= $periodoId ?>" class="btn btn-secondary">
            Cancelar
        </a>

        <button type="submit" class="btn btn-success">
            Confirmar Pesagem
        </button>
    </form>
</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
