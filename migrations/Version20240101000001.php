<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240101000001 extends AbstractMigration
{

    public function up(Schema $schema): void
    {
        // Veterinários
        $this->addSql("INSERT INTO veterinarian (name, crmv) VALUES ('João Silva', 'GO-12345')");
        $this->addSql("INSERT INTO veterinarian (name, crmv) VALUES ('Erick Maia', 'TO-54321')");
        $this->addSql("INSERT INTO veterinarian (name, crmv) VALUES ('Carlos Pereira', 'MG-99887')");

        // Fazendas
        $this->addSql("INSERT INTO farm (name, responsavel, tamanho) VALUES ('Fazenda São João', 'José Pereira', 100.00)");
        $this->addSql("INSERT INTO farm (name, responsavel, tamanho) VALUES ('Fazenda Cachoeira', 'Maria Lúcia', 200.50)");

        // Busca os IDs inseridos para usar nos vínculos
        $this->addSql("SET @farm1 = (SELECT id FROM farm WHERE name = 'Fazenda São João')");
        $this->addSql("SET @farm2 = (SELECT id FROM farm WHERE name = 'Fazenda Cachoeira')");
        $this->addSql("SET @vet1 = (SELECT id FROM veterinarian WHERE crmv = 'GO-12345')");
        $this->addSql("SET @vet2 = (SELECT id FROM veterinarian WHERE crmv = 'TO-54321')");
        $this->addSql("SET @vet3 = (SELECT id FROM veterinarian WHERE crmv = 'MG-99887')");

        // Vínculos fazenda-veterinário
        $this->addSql("INSERT INTO farm_veterinarian (farm_id, veterinarian_id) VALUES (@farm1, @vet1)");
        $this->addSql("INSERT INTO farm_veterinarian (farm_id, veterinarian_id) VALUES (@farm1, @vet2)");
        $this->addSql("INSERT INTO farm_veterinarian (farm_id, veterinarian_id) VALUES (@farm2, @vet3)");

        // Animais
        $this->addSql("INSERT INTO cow (codigo, litros_leite_por_semana, racao_por_semana, peso, data_nascimento, abatido, abatido_em, fazenda_id) VALUES ('BOV-001', 80.00, 200.00, 250.00, '2021-03-10', 0, NULL, @farm1)");
        $this->addSql("INSERT INTO cow (codigo, litros_leite_por_semana, racao_por_semana, peso, data_nascimento, abatido, abatido_em, fazenda_id) VALUES ('BOV-002', 30.00, 400.00, 300.00, '2018-05-10', 0, NULL, @farm1)");
        $this->addSql("INSERT INTO cow (codigo, litros_leite_por_semana, racao_por_semana, peso, data_nascimento, abatido, abatido_em, fazenda_id) VALUES ('BOV-003', 90.00, 150.00, 200.00, '2022-08-15', 0, NULL, @farm2)");
    }

    public function down(Schema $schema): void
    {
        $this->addSql("DELETE FROM cow WHERE codigo IN ('BOV-001', 'BOV-002', 'BOV-003')");
        $this->addSql("DELETE FROM farm_veterinarian WHERE farm_id IN (1, 2)");
        $this->addSql("DELETE FROM farm WHERE name IN ('Fazenda São João', 'Fazenda Cachoeira')");
        $this->addSql("DELETE FROM veterinarian WHERE crmv IN ('GO-12345', 'TO-54321', 'MG-99887')");
    }
}