# Wallet Core - Sistema de Carteira Digital

![CI Status](https://github.com/caalb/wallet-core/workflows/CI/badge.svg)

## 📋 Pré-requisitos

- Docker 24+ e Docker Compose v2
- Git

## 🚀 Como Rodar o Projeto

### 1. Clone o repositório

```bash
git clone <url-do-repositorio>
cd wallet
```

### 2. Configure as variáveis de ambiente

```bash
cp .env.example .env
```

### 3. Inicie os containers

```bash
docker compose up -d --build
```

Isso irá iniciar:

- **Hyperf** (aplicação) na porta `9501`
- **MySQL** na porta `3306`
- **Redis** na porta `6379`
- **RabbitMQ** na porta `5672` (Management UI: `15672`)

### 4. Execute as migrations

```bash
docker compose exec hyperf php bin/hyperf.php migrate
```

### 5. Verifique se está rodando

```bash
curl http://localhost:9501/health
```

### Usando Docker Compose diretamente

```bash
# Iniciar containers
docker compose up -d

# Parar containers
docker compose down

# Ver logs
docker compose logs -f

# Executar comandos no container
docker compose exec hyperf php bin/hyperf.php <comando>

# Acessar shell do container
docker compose exec hyperf sh
```

## 🔗 Acessos aos Serviços

- **API**: http://localhost:9501
- **Documentação da API (Swagger)**: http://localhost:9501/docs
- **RabbitMQ Management**: http://localhost:15672
  - Usuário: `wallet-core`
  - Senha: `wallet-core-secret`
- **MySQL**: `localhost:3306`
  - Database: `wallet-core`
  - Usuário: `wallet-core`
  - Senha: `wallet-core-secret`

## 📚 Documentação da API

A documentação completa da API está disponível através do Swagger UI em: http://localhost:9501/docs

### Endpoints Disponíveis

#### Health Check

- `GET /health` - Verifica o status da API

#### Autenticação

- `POST /api/auth/register` - Registra novo usuário
- `POST /api/auth/login` - Autentica usuário e retorna token JWT

#### Transações

- `POST /api/v1/transfer` - Realiza transferência entre carteiras (requer autenticação)

### Swagger UI

A documentação interativa está disponível em:

**http://localhost:9502/swagger**

> **Nota:** O Swagger UI permite testar todos os endpoints da API diretamente no navegador.

### Arquivo OpenAPI

O arquivo de especificação OpenAPI 3.0 está disponível em: `docs/openapi.yaml`

## Variáveis de Ambiente

As variáveis de ambiente estão configuradas no `docker-compose.yml`. Para desenvolvimento local, copie o arquivo `.env.example` para `.env` e ajuste as configurações conforme necessário:

```bash
cp .env.example .env
```

## Limpeza

```bash
# Parar e remover containers
docker compose down

# Parar, remover containers e volumes (apaga dados)
docker compose down -v

# Rebuild completo
make rebuild
```
