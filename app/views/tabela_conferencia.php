<?php require_once __DIR__ . '/header.php'; ?>

<div class="container mt-4">

    <h2 class="mb-4">Conferência de Pesagens</h2>

    <!-- Informações básicas da operação -->
    <div class="alert alert-secondary">
        <strong>Navio:</strong> <?= htmlspecialchars($navio) ?> &nbsp; |
        <strong>Início:</strong> <?= htmlspecialchars($inicio) ?> &nbsp; |
        <strong>Fim:</strong> <?= htmlspecialchars($fim) ?> &nbsp; |
        <strong>Produto:</strong> <?= htmlspecialchars($produto) ?> &nbsp; |
        <strong>Recinto:</strong> <?= htmlspecialchars($recinto) ?>
    </div>

    <!-- Botões principais -->
    <div class="mb-3 d-flex gap-2">
        <button id="btnCapture" class="btn btn-primary">
            Capturar Pesagens
        </button>

        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#modalLancamentoManual">
            Lançamento Manual
        </button>

        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#modalParalisacoes">
            Paralisações
        </button>

        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#modalEdicaoMassa">
            Edição em Massa
        </button>
    </div>

    <!-- Área onde a tabela será atualizada -->
    <div id="tableArea">
        <div class="alert alert-info">
            Clique em <b>Capturar Pesagens</b> para buscar os registros.
        </div>
    </div>

</div>

<!-- Modal de Lançamento Manual -->
<div class="modal fade" id="modalLancamentoManual" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Lançamento Manual</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p>Versão inicial — em breve implementaremos igual ao conferentes.app.</p>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Paralisações -->
<div class="modal fade" id="modalParalisacoes" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Paralisações</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p>Versão inicial — ainda iremos implementar o sistema completo de paralisações.</p>
      </div>
    </div>
  </div>
</div>

<!-- Modal de Edição em Massa -->
<div class="modal fade" id="modalEdicaoMassa" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Edição em Massa</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <p>Versão inicial — em breve igual ao conferentes.app.</p>
      </div>
    </div>
  </div>
</div>

<script>
// Simula captura temporária até ligarmos ao Poseidon
document.getElementById("btnCapture").addEventListener("click", function() {
    document.getElementById("tableArea").innerHTML = `
        <div class="alert alert-success">Simulação: dados capturados com sucesso!</div>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Placa</th>
                    <th>Entrada</th>
                    <th>Saída</th>
                    <th>Peso Líquido</th>
                </tr>
            </thead>
            <tbody>
                <tr><td>ABC1234</td><td>07:10</td><td>07:22</td><td>28.500</td></tr>
                <tr><td>XYZ9988</td><td>07:15</td><td>07:30</td><td>29.200</td></tr>
            </tbody>
        </table>
    `;
});
</script>

<?php require_once __DIR__ . '/footer.php'; ?>
