# to-do-list-petiko

Este é um projeto Laravel para gerenciar uma lista de tarefas.

## Detalhes do Projeto:

**Nome do Projeto:** to-do-list-petiko

**Breve Descrição:** Uma aplicação web para gerenciar tarefas pessoais, permitindo aos usuários criar, visualizar, atualizar, excluir e marcar tarefas como concluídas.

**Propósito/Objetivo:** Fornecer uma ferramenta simples e eficaz para organização de tarefas diárias, ajudando usuários a manterem o controle de suas atividades e prazos.

## Tecnologias Principais:

*   PHP (Laravel Framework)
*   MySQL (para banco de dados)
*   Blade (para views)
*   Tailwind CSS (para estilização)
*   Inertia.js (para SPA-like experience)
*   Vue.js (para componentes front-end)

## Funcionalidades Principais:

*   Autenticação de Usuários (Registro, Login, Logout).
*   Gerenciamento de Tarefas (CRUD: Create, Read, Update, Delete).
*   Marcação de tarefas como concluídas/não concluídas.
*   Filtragem e busca de tarefas.
*   Exportação de tarefas para CSV.
*   Sistema de permissões (Admin/Usuário Comum).

## Pré-requisitos:

*   PHP >= 8.1
*   Composer
*   Node.js e npm/Yarn
*   MySQL/SQLite (ou outro SGBD de sua escolha)
*   Git

## Instruções de Instalação e Configuração:

1.  **Clonar o Repositório:**
    ```bash
    git clone https://github.com/seu-usuario/to-do-list-petiko.git
    cd to-do-list-petiko
    ```

2.  **Instalar Dependências PHP:**
    ```bash
    composer install
    ```

3.  **Configurar o Arquivo .env:**
    *   Copie o arquivo `.env.example` para `.env`:
        ```bash
        cp .env.example .env
        ```
    *   Edite o arquivo `.env` para configurar suas credenciais de banco de dados e outras variáveis de ambiente. Por exemplo:
        ```env
        DB_CONNECTION=mysql
        DB_HOST=127.0.0.1
        DB_PORT=3306
        DB_DATABASE=to_do_list_petiko
        DB_USERNAME=root
        DB_PASSWORD=
        ```

4.  **Gerar a Application Key:**
    ```bash
    php artisan key:generate
    ```

5.  **Executar as Migrations do Banco de Dados:**
    ```bash
    php artisan migrate
    ```

6.  **Executar Seeders :**
    ```bash
    php artisan db:seed
    ```

7.  **Instalar Dependências Front-end:**
    ```bash
    npm install # ou yarn install
    npm run dev # ou npm run build para produção
    ```

8.  **Iniciar o Servidor de Desenvolvimento:**
    ```bash
    php artisan serve
    ```

9.  **Acessar a Aplicação:** Abra seu navegador e vá para `http://127.0.0.1:8000/` (ou a porta indicada pelo `php artisan serve`).

## Como Usar:

Após iniciar a aplicação, você pode se registrar como um novo usuário ou fazer login. No dashboard, você poderá criar novas tarefas, visualizar suas tarefas existentes, editá-las, excluí-las e marcá-las como concluídas. Usuários com perfil de administrador terão acesso a funcionalidades adicionais, como a exportação de todas as tarefas para CSV.

Irá existir usuários criados por padrão pelo seeder

Usuário Admin
login: admin@example.com
senha: password

Usuário Comum
login: user@example.com
senha: password