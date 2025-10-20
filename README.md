# 🕐 TimeBank

Uma plataforma de troca de horas entre profissionais baseada no conceito de "banco de tempo", onde o tempo investido é a moeda de troca, criando uma economia colaborativa.

## 📖 Sobre o Projeto

O **TimeBank** é uma plataforma que permite a troca de horas de serviço entre profissionais de diferentes áreas. A lógica é simples, mas poderosa:

- Um usuário pode fazer pedidos de um serviço em troca de um serviço seu
- O outro usuário pode aceitar ou não essa troca de serviços
- Não importa o valor monetário, o que importa é o tempo investido e a troca de serviços

### Exemplo Prático

> **João** ajuda **Maria** a configurar um site por **2 horas** → em troca, **Maria** ajuda **João** com **2 horas** de design gráfico.

## 🚀 Tecnologias Utilizadas

- **PHP 8+** - Linguagem principal
- **Slim Framework 4** - Microframework para construção da API REST
- **Illuminate Database (Eloquent ORM)** - ORM para interação com banco de dados
- **PostgreSQL 15** - Banco de dados relacional
- **Firebase PHP-JWT** - Autenticação e autorização via tokens JWT
- **Swagger/OpenAPI** - Documentação interativa da API
- **vlucas/phpdotenv** - Gerenciamento de variáveis de ambiente
- **Docker & Docker Compose** - Containerização e orquestração
- **pgAdmin** - Interface de administração do PostgreSQL

## 📋 Pré-requisitos

- Docker e Docker Compose instalados ou Postgresql instalado
- PHP 8+ (para rodar localmente sem Docker)
- Composer

## 🔧 Instalação e Configuração

### 1. Clone o repositório

```bash
git clone <seu-repositorio>
cd time-bank
```

### 2. Instale as dependências

```bash
composer install
```

### 3. Configure as variáveis de ambiente

Copie o arquivo `.env` e ajuste conforme necessário:

```bash
cp .env.example .env
```

### 4. Suba os containers Docker

```bash
docker compose up -d
```

Isso iniciará:
- PostgreSQL na porta `5432`
- pgAdmin na porta `5050` (acesse via `http://localhost:5050`)

### 5. Execute as migrations

```bash
php migrate.php
```

### 6. Inicie o servidor PHP

```bash
php -S localhost:8080 -t public
```

A API estará disponível em `http://localhost:8080`

## 📚 Documentação da API

A documentação interativa da API está disponível através do Swagger UI:

**Acesse:** `http://localhost:8080/docs.html`

A documentação inclui:
- Todos os endpoints disponíveis
- Parâmetros e corpo das requisições
- Respostas esperadas
- Possibilidade de testar os endpoints diretamente no navegador

### Endpoints Principais

#### Autenticação
- `POST /register` - Registrar novo usuário
- `POST /login` - Autenticar e obter token JWT
- `GET /me` - Obter informações do usuário autenticado

#### Transações
- `POST /transactions` - Criar nova proposta de troca de horas
- `GET /transactions` - Listar todas as transações do usuário
- `GET /transactions/incoming` - Listar transações pendentes recebidas
- `PATCH /transactions/{id}/accept` - Aceitar uma transação
- `PATCH /transactions/{id}/reject` - Rejeitar uma transação
- `GET /transactions/available-users` - Listar usuários disponíveis

## Estrutura do Projeto

```
time-bank/
├── public/
│   └── index.php              # Ponto de entrada da aplicação
├── src/
│   ├── Config/
│   │   └── database/          # Configuração e migrations
│   ├── Controllers/           # Controladores da aplicação
│   ├── Handlers/              # Tratamento de erros
│   ├── Middlewares/           # Middlewares (auth, etc)
│   ├── Models/                # Models Eloquent
│   └── Routes/                # Definição de rotas
├── docker-compose.yml         # Configuração Docker
├── migrate.php                # Script de migração
└── composer.json              # Dependências do projeto
```

## Autenticação

A API utiliza JWT (JSON Web Tokens) para autenticação. Após o login, inclua o token no header das requisições:

```
Authorization: Bearer {seu-token-jwt}
```

## 🎯 Possibilidades Futuras

- Adicionar campo de habilidades para os usuários
- Sistema de avaliações pós serviço
- Relatórios de transações
- Filtros e buscas avançadas
