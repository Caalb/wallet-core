# wallet-core - Aplicação Hyperf

Aplicação desenvolvida com o framework Hyperf e PostgreSQL, totalmente containerizada com Docker.

## 🚀 Tecnologias

- **Hyperf**: Framework PHP baseado em Swoole para aplicações de alta performance
- **PostgreSQL 15**: Banco de dados relacional
- **Docker & Docker Compose**: Containerização da aplicação
- **PHP 8.1**: Versão mínima do PHP
- **Swoole**: Extension PHP para programação assíncrona

## 📋 Pré-requisitos

- Docker (versão 20.10 ou superior)
- Docker Compose (versão 2.0 ou superior)

## 🔧 Instalação e Configuração

1. **Clone o repositório** (se ainda não estiver no diretório):

```bash
git clone <seu-repositorio>
cd wallet-core
```

2. **Configure as variáveis de ambiente**:

O arquivo `.env` já está configurado com os valores padrão. Se desejar, você pode alterá-los:

```env
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=wallet-core
DB_USERNAME=wallet-core
DB_PASSWORD=picpay_secret
```

3. **Construa e inicie os containers**:

```bash
docker compose up -d --build
```

4. **Verifique se os containers estão rodando**:

```bash
docker compose ps
```

## 🎮 Comandos Úteis

### Iniciar os containers

```bash
docker compose up -d
```

### Parar os containers

```bash
docker compose down
```

### Ver logs

```bash
# Todos os logs
docker-compose logs -f

# Logs apenas do Hyperf
docker-compose logs -f hyperf

# Logs apenas do PostgreSQL
docker-compose logs -f pos gres
```

### Executar comandos dentro do container Hyperf

```bash
docker compose exec hyperf sh
```

### Instalar novas dependências

```bash
docker compose exec hyperf composer require <package-name>
```

### Executar migrations (quando criadas)

```bash
docker compose exec hyperf php bin/hyperf.php migrate
```

### Gerar um novo controller

```bash
docker compose exec hyperf php bin/hyperf.php gen:controller NomeController
```

### Gerar um novo model

```bash
docker compose exec hyperf php bin/hyperf.php gen:model NomeModel
```

## 📡 Endpoints

A aplicação estará disponível em:

- **API**: http://localhost:9501
- **PostgreSQL**: localhost:5432

### Testando a aplicação

```bash
curl http://localhost:9501
```

## 🗃️ Estrutura do Projeto

```
wallet-core/
├── app/                    # Código da aplicação
│   ├── Controller/        # Controllers
│   ├── Model/            # Models
│   ├── Middleware/       # Middlewares
│   └── Exception/        # Exception Handlers
├── config/                # Arquivos de configuração
│   └── autoload/         # Configurações autoload
├── bin/                   # Scripts executáveis
├── runtime/              # Arquivos temporários e cache
├── test/                 # Testes
├── docker-compose.yml    # Configuração Docker Compose
├── Dockerfile            # Configuração do container
└── .env                  # Variáveis de ambiente
```

## 🔍 Banco de Dados

### Conectar ao PostgreSQL

```bash
docker-compose exec postgres psql -U wallet-core -d wallet-core
```

### Comandos úteis do PostgreSQL

```sql
-- Listar todas as tabelas
\dt

-- Descrever uma tabela
\d nome_da_tabela

-- Sair do psql
\q
```

## 🧪 Testes

Para executar os testes:

```bash
docker-compose exec hyperf composer test
```

## 📚 Documentação Adicional

- [Documentação Oficial do Hyperf](https://hyperf.wiki/)
- [Hyperf no GitHub](https://github.com/hyperf/hyperf)
- [PostgreSQL Documentation](https://www.postgresql.org/docs/)

## 🛠️ Desenvolvimento

### Hot Reload

O Hyperf suporta hot reload em modo de desenvolvimento. Para habilitar, você pode usar o watcher:

```bash
docker-compose exec hyperf php bin/hyperf.php server:watch
```

### Debug

Para habilitar o modo debug, ajuste no arquivo `.env`:

```env
APP_ENV=dev
SCAN_CACHEABLE=false
```

## 🤝 Contribuindo

1. Faça um fork do projeto
2. Crie uma branch para sua feature (`git checkout -b feature/AmazingFeature`)
3. Commit suas mudanças (`git commit -m 'Add some AmazingFeature'`)
4. Push para a branch (`git push origin feature/AmazingFeature`)
5. Abra um Pull Request

## 📝 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 👥 Autores

- Seu Nome - PUC

## 🎯 Roadmap

- [ ] Implementar autenticação JWT
- [ ] Criar migrations do banco de dados
- [ ] Adicionar testes unitários e de integração
- [ ] Implementar CI/CD
- [ ] Adicionar documentação da API com Swagger

## ❓ FAQ

**P: Como atualizar as dependências?**

```bash
docker-compose exec hyperf composer update
```

**P: Como limpar o cache?**

```bash
docker-compose exec hyperf rm -rf runtime/container
```

**P: Como rebuild os containers?**

```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```
