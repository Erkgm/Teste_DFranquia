#  Teste_DFranquia — Sistema de Controle de Fazenda de Bovinos


---

##  Como iniciar o projeto

### Dependências
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) instalado
- [Git](https://git-scm.com/downloads) instalado
- Symfony 6.4 instalado
- MySQL 8.0
- PHP 8.2

### Passo a passo

**1. Clone o repositório:**
```bash
git clone https://github.com/Erkgm/Teste_DFranquia.git
cd Teste_DFranquia
```

**2. Suba os containers:**
```bash
docker compose -f compose.yaml up -d --build
docker exec fazenda_php composer install
```

**3. Sincronize o storage de migrations:**
```bash
docker exec fazenda_php php bin/console doctrine:migrations:sync-metadata-storage
```

**4. Execute as migrations (cria tabelas e insere dados de exemplo):**
```bash
docker exec fazenda_php php bin/console doctrine:migrations:migrate --no-interaction
```

**5. Acesse o sistema:**
http://localhost:8080

---

##  Comandos Docker

| Comando | Descrição |
|---|---|
| `docker compose -f compose.yaml up -d` | Inicia os containers |
| `docker compose -f compose.yaml stop` | Para os containers  |
| `docker compose -f compose.yaml down` | Remove os containers  |
| `docker compose -f compose.yaml down -v` | Remove containers e dados |
| `docker compose -f compose.yaml ps` | Lista containers em execução |
| `docker exec fazenda_php php bin/console cache:clear` | Limpa o cache |

---

##  Funcionalidades

### Cadastros 
- **Veterinários** — Nome e CRMV único
- **Fazendas** — Nome único, tamanho em hectares, responsável, vínculo com veterinários (N:N)
- **Animais** — Código único, leite, ração, peso, nascimento, fazenda (N:1)

### Regras de Negócio
- CRMV único por veterinário
- Nome único por fazenda
- Capacidade máxima: 18 animais por hectare
- Código único entre animais vivos
- Data de nascimento não pode ser futura
- Validação de capacidade ao cadastrar e editar animais

### Abate
Um animal é elegível para abate quando atende ao menos uma das condições:


|   | Condição  |
|---|---|
| 1 | Mais de 5 anos de idade|
| 2 | Menos de 40L de leite/semana |
| 3 | Menos de 70L de leite/semana & mais de 50kg ração/dia |
| 4 | Peso superior a 18 arrobas (270kg) |

### Dashboard
- Total de leite produzido por semana
- Total de ração consumida por semana
- Animais com até 1 ano e consumo > 500kg ração/semana
- Contagem de animais elegíveis para abate


