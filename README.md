# Blog

Blog corporativo com publicação restrita a uma equipe controlada por convite: um administrador convida colaboradores, cada um escreve e gerencia os próprios posts, e o público em geral só enxerga o que foi publicado — sem cadastro aberto, sem rascunho vazando, sem área administrativa visível.

## Funcionalidades

- **Site público** — listagem paginada de posts publicados (mais recente primeiro), página individual por post, nada de rascunho aparece nem por acaso
- **Convites com validade** — admin gera um link de convite (token aleatório, expira em 48h, uso único); quem recebe o link define nome e senha e já entra logado
- **Papéis (admin / colaborador)** — middleware de role + Policies controlam quem pode editar/excluir o quê; colaborador só mexe nos próprios posts, admin mexe em tudo
- **CRUD de posts** — criação e edição via Livewire, upload de imagem (com validação real de tipo e tamanho), publicar/despublicar, exclusão, slug gerado automaticamente a partir do título
- **Gestão de usuários** — admin lista todos os usuários, convida novos colaboradores, remove acesso de alguém sem apagar o histórico de posts dessa pessoa
- **Autenticação própria** — login por email/senha em rota escondida (sem link público), rate limiting, sessão invalidada no logout

## Tecnologias

- **Laravel 13** — framework, com PHP 8.4
- **Livewire 4** — Single File Components para toda a parte interativa (formulários, listagens com ações)
- **Tailwind CSS 4** — estilização
- **MySQL** — persistência
- **Pest** — testes automatizados (61 testes)
- **Herd** — ambiente local

## Telas

### Site público

<!-- screenshot: home -->
<!-- salve em docs/screenshots/home.png -->

<!-- screenshot: post individual -->
<!-- salve em docs/screenshots/post.png -->

### Painel do colaborador

<!-- screenshot: painel -->
<!-- salve em docs/screenshots/painel.png -->

### Admin (usuários e convites)

<!-- screenshot: admin -->
<!-- salve em docs/screenshots/admin.png -->

## Decisões técnicas

**Cadastro só por convite, nunca aberto.** Isso não é uma rede social — é o blog de uma equipe, e quem decide quem escreve é o admin. Cada convite é um token aleatório de 40 caracteres, expira em 48h e só pode ser usado uma vez. Não existe (e nunca existiu) uma tela pública de "criar conta".

**Rota de login escondida, sem link em lugar nenhum do site.** Um leitor comum do blog nunca precisa logar. Colocar um botão "Entrar" visível no site público só serve pra indicar pra quem está procurando onde fica a porta de entrada da área administrativa. A rota (`/painel/login`) existe e funciona normalmente pra quem já sabe o caminho — só não é anunciada.

**`is_active` em vez de soft delete pra remover acesso.** Quando o acesso de um colaborador é removido, os posts dele continuam existindo no site público com o nome dele como autor. Soft delete (`deleted_at`) aplicaria um global scope que esconderia o usuário de qualquer relação carregada por padrão, complicando a exibição do autor à toa. Uma coluna booleana resolve exatamente o problema pedido — bloquear login — sem mexer em mais nada.

**Rate limiting em toda ação sensível, não só no login.** Os três pontos onde alguém de fora (ou uma sessão comprometida) conseguiria abusar de uma ação repetidamente são: login, geração de convite e cadastro via convite. Os três usam o `RateLimiter` nativo do Laravel, cada um com uma janela proporcional ao risco (login e cadastro por convite são os mais expostos, por serem acessíveis sem autenticação).

**Policy checada na rota e dentro do componente Livewire.** Um componente Livewire mantém estado entre requisições — o middleware de role da rota garante que só admin/colaborador autenticado chega até a página, mas não sabe decidir "esse colaborador pode editar *este* post específico?". Por isso toda ação que muda dado (`save`, `delete`, `togglePublish`, `removeAccess`) revalida a Policy internamente, além da checagem já feita na rota.

## Como rodar localmente

Requisitos: PHP 8.3+, Composer, Node 20+, MySQL (ou MariaDB).

```bash
git clone <url-do-repositorio> blog
cd blog

composer install
npm install

cp .env.example .env
php artisan key:generate
```

Configure no `.env` as credenciais do banco (`DB_DATABASE=blog` por padrão) e depois:

```bash
php artisan migrate
php artisan storage:link

npm run build
php artisan serve
```

A aplicação fica disponível em `http://localhost:8000` (ou no domínio configurado, se estiver usando [Herd](https://herd.laravel.com)).

Não existe seeder de admin — crie o primeiro usuário administrador manualmente:

```bash
php artisan tinker --execute="App\Models\User::factory()->admin()->create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => 'sua-senha']);"
```

A partir daí, todo novo colaborador entra pela tela **Convidar colaborador** dentro de `/admin`.

## Testes

```bash
php artisan test
```

## Licença

MIT
