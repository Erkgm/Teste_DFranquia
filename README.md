#  Teste_DFranquia — Sistema de Controle de Fazenda de Bovinos


---

##  Como iniciar o projeto

### Dependências / pré-requisitos
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

> As migrations em ordem:
> - ..17 cria todas as tabelas do banco
> - ..55 padronização dos nomes das colunas para inglês
> - ..56 popula o banco com dados de exemplo para testes


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



### Alterações

**`createListQueryBuilder`** nos repositories — para centralizar as queries de listagem com filtro/busca, evitando duplicação entre controller e service.

**`findByNameExcluding` / `findByCrmvExcluding` / `findLiveByCode`** — ao editar um registro, a validação de unicidade precisa ignorar o próprio registro sendo editado. Sem os métodos, salvar sem alterar o nome/CRMV geraria falso positivo de duplicidade.

**Por que `findByNameExcluding` em vez de `findOneBy(['name' => $name])`**? — o método padrão `findOneBy`e estava buscando qualquer registro com aquele nome no banco. No fluxo de edição, se o usuário salvasse sem alterar o nome, o sistema encontraria o próprio registro e retornaria erro de duplicidade. O método `findByNameExcluding` adiciona um `AND id != :id` na query, ignorando o registro atual e evitando esse falso positivo. O mesmo ao `findByCrmvExcluding` para veterinários e ao `findLiveByCode` com `excludeId` para animais.

**`CowService`** — concentra todas as regras de abate (`canBeSlaughtered`, `getSlaughterReasons`) separadas da Entity

**`try/catch` nos services — garantindo erros inesperados do banco sejam capturados e retornados como mensagem ao usuário


##  Dados de exemplo

**Credenciais do banco (Docker):**
- **Usuário:** root
- **Senha:** 
- **Host:** localhost
- **Porta:** 3306
- **Banco:** fazenda_bovinos

```bash
docker exec -it fazenda_db mysql -uroot -proot fazenda_bovinos
```

**SQL de exemplo (1 fazenda, 1 veterinário, 18 animais):**

```sql
-- Veterinário
INSERT INTO veterinarian (name, crmv) VALUES ('João Silva', 'GO-12345');

-- fazenda
INSERT INTO farm (name, responsible, size) VALUES ('Fazenda São João', 'José Pereira', 100.00);

-- fazenda-veterinário
SET @farm1 = (SELECT id FROM farm WHERE name = 'Fazenda São João');
SET @vet1 = (SELECT id FROM veterinarian WHERE crmv = 'GO-12345');
INSERT INTO farm_veterinarian (farm_id, veterinarian_id) VALUES (@farm1, @vet1);

-- 18 Animais 
INSERT INTO cow (code, milk_liters_per_week, ration_kg_per_week, weight_kg, birth_date, slaughtered, slaughtered_at, farm_id) VALUES
('BOV-001', 80.00, 200.00, 250.00, '2021-03-10', 0, NULL, @farm1),
('BOV-002', 90.00, 150.00, 200.00, '2022-08-15', 0, NULL, @farm1),
('BOV-003', 75.00, 180.00, 230.00, '2021-06-20', 0, NULL, @farm1),
('BOV-004', 85.00, 190.00, 240.00, '2020-11-05', 0, NULL, @farm1),
('BOV-005', 70.00, 170.00, 210.00, '2022-01-15', 0, NULL, @farm1),
('BOV-006', 95.00, 210.00, 260.00, '2021-09-30', 0, NULL, @farm1),
('BOV-007', 88.00, 195.00, 245.00, '2022-03-25', 0, NULL, @farm1),
('BOV-008', 72.00, 175.00, 215.00, '2021-12-10', 0, NULL, @farm1),
('BOV-009', 83.00, 185.00, 235.00, '2022-07-08', 0, NULL, @farm1),
('BOV-010', 91.00, 205.00, 255.00, '2021-04-18', 0, NULL, @farm1),
('BOV-011', 78.00, 182.00, 225.00, '2022-05-22', 0, NULL, @farm1),
('BOV-012', 86.00, 192.00, 242.00, '2021-08-14', 0, NULL, @farm1),
('BOV-013', 74.00, 178.00, 218.00, '2022-02-28', 0, NULL, @farm1),
('BOV-014', 92.00, 208.00, 258.00, '2021-01-07', 0, NULL, @farm1),
('BOV-015', 81.00, 188.00, 238.00, '2022-09-19', 0, NULL, @farm1),
('BOV-016', 89.00, 198.00, 248.00, '2021-07-03', 0, NULL, @farm1),
('BOV-017', 76.00, 176.00, 222.00, '2022-04-11', 0, NULL, @farm1),
('BOV-018', 94.00, 212.00, 262.00, '2021-10-25', 0, NULL, @farm1);
```
---


