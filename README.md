# Wallet Core - Sistema de Carteira Digital

## 📋 Pré-requisitos

- Docker 24+ e Docker Compose v2
- Git

## 🚀 Como Rodar o Projeto

### 1. Clone o repositório

```bash
git clone <url-do-repositorio>
cd wallet
```

### 2. Inicie os containers

```bash
docker compose up -d --build
```

Isso irá iniciar:

- **Hyperf** (aplicação) na porta `9501`
- **MySQL** na porta `3306`
- **Redis** na porta `6379`
- **RabbitMQ** na porta `5672` (Management UI: `15672`)

### 3. Execute as migrations

```bash
docker compose exec hyperf php bin/hyperf.php migrate
```

### 4. Verifique se está rodando

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
- **RabbitMQ Management**: http://localhost:15672
  - Usuário: `wallet-core`
  - Senha: `wallet-core-secret`
- **MySQL**: `localhost:3306`
  - Database: `wallet-core`
  - Usuário: `wallet-core`
  - Senha: `wallet-core-secret`

## Variáveis de Ambiente

As variáveis de ambiente estão configuradas no `docker-compose.yml`. Para desenvolvimento local, você pode criar um arquivo `.env` se necessário.

## Limpeza

```bash
# Parar e remover containers
docker compose down

# Parar, remover containers e volumes (apaga dados)
docker compose down -v

# Rebuild completo
make rebuild
```
