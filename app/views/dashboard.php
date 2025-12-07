<section class="dashboard">
    <h1>Dashboard inicial</h1>
    <p>
        Olá, <strong><?php echo htmlspecialchars($_SESSION['user'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong>!
    </p>

    <p>
        Esta é a versão 0.1 do <em>Sistema Conferentes PLUS</em>.
    </p>

    <p>
        Próximos passos previstos:
    </p>
    <ul>
        <li>Adicionar tela para informar navio / início / fim / produto / recinto.</li>
        <li>Implementar captura automática no Poseidon (consulta de relatórios).</li>
        <li>Salvar os registros em banco ou gerar arquivos para importação.</li>
    </ul>

    <p class="note">
        Por enquanto é só uma base segura com login simples,
        rodando no Render, pronta para evoluir.
    </p>
</section>
