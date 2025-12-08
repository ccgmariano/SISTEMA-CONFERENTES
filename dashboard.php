<?php
session_start();
require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-4" style="max-width: 700px;">

    <?php if (isset($_SESSION['operacao_atual'])): ?>
        <?php $op = $_SESSION['operacao_atual']; ?>

        <div class="card mb-4" style="padding: 16px; border: 1px solid #ddd; border-radius: 8px;">
            <h3 class="mb-3">Operação Atual</h3>

            <p><strong>Empresa:</strong> <?= htmlspecialchars($op['empresa']) ?></p>
            <p><strong>Navio:</strong> <?= htmlspecialchars($op['navio']) ?></p>
            <p><strong>Produto:</strong> <?= htmlspecialchars($op['produto']) ?></p>
            <p><strong>Recinto:</strong> <?= htmlspecialchars($op['recinto']) ?></p>
            <p><strong>Tipo de Operação:</strong> <?= htmlspecialchars($op['tipo_operacao']) ?></p>
            <p><small>Criado em: <?= htmlspecialchars($op['criado_em']) ?></small></p>
        </div>

    <?php else: ?>

        <div class="alert alert-info mb-4">
            Nenhuma operação ativa. Clique em <strong>Nova Operação</strong> para iniciar.
        </div>

    <?php endif; ?>

    <h2 class="mt-4">Dashboard inicial</h2>

    <p>Olá, <strong>conferente</strong>!</p>

    <p>Esta é a versão 0.1 do <strong>Sistema Conferentes PLUS</strong>.</p>

    <p>Próximos passos previstos:</p>
    <ul>
        <li>Adicionar tela para informar navio / início / fim / produto / recinto.</li>
        <li>Implementar captura automática no Poseidon (consulta de relatórios).</li>
        <li>Salvar os registros em banco ou gerar arquivos para importação.</li>
    </ul>

    <p>Por enquanto é só uma base segura com login simples, rodando no Render, pronta para evoluir.</p>

    <p class="mt-4"><small>Sistema Conferentes PLUS • Versão 0.1</small></p>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
