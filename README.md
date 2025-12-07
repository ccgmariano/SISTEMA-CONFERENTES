# Sistema Conferentes PLUS (Versão 0.1)

Primeira versão mínima do sistema, apenas com:

- Tela de login (usuário/senha fixos)
- Tela de dashboard simples depois do login
- Nenhum banco de dados ainda
- Pronto para rodar em ambiente Docker (Render)

## Login padrão

- Usuário: `conferente`
- Senha: `1234`

## Estrutura

- `config.php` — configuração básica e sessão
- `index.php` — decide se redireciona para login ou dashboard
- `login.php` — página de login
- `dashboard.php` — página principal após login
- `logout.php` — sair do sistema

Pastas:

- `app/controllers` — controladores (auth, etc.)
- `app/views` — views (header, footer, telas)
- `assets/css` — estilos
- `assets/js` — scripts
- `assets/img` — imagens (vazia por enquanto)
