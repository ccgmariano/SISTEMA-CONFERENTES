<div class="card mt-4">
    <div class="card-header">
        <strong>Pesagens Encontradas</strong>
    </div>
    <div class="card-body">

        <table id="tabelaPesagens" class="table table-striped">
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>Hora</th>
                    <th>Peso</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($dadosSimulados as $linha): ?>
                <tr>
                    <td><?= $linha[0] ?></td>
                    <td><?= $linha[1] ?></td>
                    <td><?= $linha[2] ?></td>
                    <td><?= $linha[3] ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    </div>
</div>

<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function(){
    $('#tabelaPesagens').DataTable({
        paging: true,
        searching: true,
        ordering: true,
        language: {
            "sEmptyTable": "Nenhum registro encontrado",
            "sInfo": "Mostrando _START_ até _END_ de _TOTAL_ registros",
            "sLengthMenu": "_MENU_ por página",
            "sSearch": "Pesquisar:"
        }
    });
});
</script>
