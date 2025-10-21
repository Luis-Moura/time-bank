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

- **PHP 8.2** com Apache
- **Slim Framework 4** - API REST
- **Eloquent ORM** - Gerenciamento de banco de dados
- **PostgreSQL 15** - Banco de dados
- **Firebase PHP-JWT** - Autenticação JWT
- **Nginx** - Balancedor de Carga
- **Docker & Docker Compose** - Containerização

## 🔧 Instalação e Configuração

### Opção 1: Desenvolvimento Local

```bash
# Clone o repositório
git clone <seu-repositorio>
cd time-bank

# Instale dependências
composer install

# Configure o ambiente
cp .env.example .env
# Ajuste DB_HOST=localhost no .env

# Suba apenas PostgreSQL e pgAdmin
docker compose up -d time-bank-postgresql time-bank-pgadmin

# Execute migrations
php migrate.php

# Inicie o servidor
php -S localhost:8080 -t public
```

**Acessos:**
- API: `http://localhost:8080/api/v1`
- Swagger: `http://localhost:8080/docs.html`
- pgAdmin: `http://localhost:5050` (admin@admin.com / admin)

### Opção 2: Docker Completo

```bash
# Configure o ambiente
cp .env.example .env
# Use DB_HOST=time-bank-postgresql no .env

# Suba todos os serviços
docker compose up -d

# Migrations executam automaticamente
```

**Acessos:**
- API (Nginx load balancer): `http://localhost:8080/api/v1`
- Swagger: `http://localhost:8080/docs.html`
- pgAdmin: `http://localhost:5050`

**Arquitetura:** Nginx balanceia requisições entre duas instâncias da API usando algoritmo `least_conn`.

## 🔐 Autenticação

Use JWT no header das requisições:
```
Authorization: Bearer {seu-token}
```