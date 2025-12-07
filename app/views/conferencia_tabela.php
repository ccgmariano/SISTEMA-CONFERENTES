<?php
require_once __DIR__ . '/header.php';

// Garantir que os dados existam
$navio   = $navio   ?? '—';
$inicio  = $inicio  ?? '—';
$fim     = $fim     ?? '—';
$produto = $produto ?? '—';
$recinto = $recinto ?? '—';
?>

<div class="container mt-4">

    <h2 class="mb-3">Conferência de Pesagens</h2>
    <p class="text-muted">Consulta operacional baseada nos filtros informados.</p>

    <!-- Resumo da consulta -->
    <div class="card mb-4">
        <div class="card-header bg-light fw-bold">Informações da Operação</div>
        <div class="card-body row">
            <div class="col-md-6"><strong>Navio:</strong> <?= htmlspecialchars($navio) ?></div>
            <div class="col-md-6"><strong>Produto:</strong> <?= htmlspecialchars($produto) ?></div>
            <div class="col-md-6"><strong>Início:</strong> <?= htmlspecialchars($inicio) ?></div>
            <div class="col-md-6"><strong>Fim:</strong> <?= htmlspecialchars($fim) ?></div>
            <div class="col-md-6"><strong>Recinto:</strong> <?= htmlspecialchars($recinto) ?></div>
        </div>
    </div>

    <!-- Tabela moderna -->
    <div class="card">
        <div class="card-header bg-primary text-white fw-bold">Pesagens Encontradas</div>
        <div class="card-body">

            <table id="tabelaConferencia" class="display nowrap table table-striped" style="width:100%">
                <thead class="table-dark">
                    <tr>
                        <th>Data</th>
                        <th>Hora</th>
                        <th>Placa</th>
                        <th>Tara</th>
                        <th>Bruto</th>
                        <th>Líquido</th>
                        <th>Diferença</th>
                        <th>Peso Grabado</th>
                        <th>Peso Conferido</th>
                        <th>Porão</th>
                        <th>Deck</th>
                        <th>Terno</th>
                        <th>Equipamento</th>
                        <th>Operador</th>
                        <th>Conferente</th>
                        <th>Origem</th>
                        <th>Destino</th>
                        <th>Produto</th>
                        <th>Navio</th>
                        <th>Observação</th>
                    </tr>
                </thead>

                <tbody>
                    <!-- POR ENQUANTO FAKE DATA, depois substituímos pela consulta real -->
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <tr>
                            <td>2025-12-07</td>
                            <td>08:<?= sprintf("%02d", rand(10,59)) ?></td>
                            <td>ABC<?= rand(1000,9999) ?></td>
                            <td><?= rand(7000,9000) ?></td>
                            <td><?= rand(20000,30000) ?></td>
                            <td><?= rand(12000,21000) ?></td>
                            <td><?= rand(-50,50) ?></td>
                            <td><?= rand(10000,20000) ?></td>
                            <td><?= rand(10000,20000) ?></td>
                            <td><?= rand(1,6) ?></td>
                            <td><?= ['LH','A','B','C','HC'][rand(0,4)] ?></td>
                            <td><?= rand(1,4) ?></td>
                            <td><?= ['GOTTWALD 4406','LIEBHERR 01','GUINDASTE DE BORDO'][rand(0,2)] ?></td>
                            <td>Oper <?= rand(1,10) ?></td>
                            <td>Conf <?= rand(1,10) ?></td>
                            <td><?= ['A.11','A.12','COPAGRO','HIPERMODAL'][rand(0,3)] ?></td>
                            <td><?= ['CAIS','EXTERNO','PATIO VOTORANTIN'][rand(0,2)] ?></td>
                            <td><?= htmlspecialchars($produto) ?></td>
                            <td><?= htmlspecialchars($navio) ?></td>
                            <td>—</td>
                        </tr>
                    <?php endfor; ?>
                </tbody>
            </table>

        </div>
    </div>

</div>

<!-- DATATABLES -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
$(document).ready(function() {
    $('#tabelaConferencia').DataTable({
        responsive: true,
        scrollX: true,
        dom: 'Bfrtip',
        buttons: [
            'csv', 'excel', 'print'
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
        }
    });
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
