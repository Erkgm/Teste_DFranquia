<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260522234555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cow DROP FOREIGN KEY `FK_99D43F9CD4A3545F`');
        $this->addSql('DROP INDEX IDX_99D43F9CD4A3545F ON cow');
        $this->addSql('ALTER TABLE cow ADD milk_liters_per_week NUMERIC(8, 2) NOT NULL, ADD ration_kg_per_week NUMERIC(8, 2) NOT NULL, ADD weight_kg NUMERIC(8, 2) NOT NULL, DROP litros_leite_por_semana, DROP racao_por_semana, DROP peso, CHANGE codigo code VARCHAR(50) NOT NULL, CHANGE data_nascimento birth_date DATE NOT NULL, CHANGE abatido slaughtered TINYINT NOT NULL, CHANGE abatido_em slaughtered_at DATETIME DEFAULT NULL, CHANGE fazenda_id farm_id INT NOT NULL');
        $this->addSql('ALTER TABLE cow ADD CONSTRAINT FK_99D43F9C65FCFA0D FOREIGN KEY (farm_id) REFERENCES farm (id)');
        $this->addSql('CREATE INDEX IDX_99D43F9C65FCFA0D ON cow (farm_id)');
        $this->addSql('ALTER TABLE farm CHANGE responsavel responsible VARCHAR(150) NOT NULL, CHANGE tamanho size NUMERIC(10, 2) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cow DROP FOREIGN KEY FK_99D43F9C65FCFA0D');
        $this->addSql('DROP INDEX IDX_99D43F9C65FCFA0D ON cow');
        $this->addSql('ALTER TABLE cow ADD litros_leite_por_semana NUMERIC(8, 2) NOT NULL, ADD racao_por_semana NUMERIC(8, 2) NOT NULL, ADD peso NUMERIC(8, 2) NOT NULL, DROP milk_liters_per_week, DROP ration_kg_per_week, DROP weight_kg, CHANGE code codigo VARCHAR(50) NOT NULL, CHANGE birth_date data_nascimento DATE NOT NULL, CHANGE slaughtered abatido TINYINT NOT NULL, CHANGE slaughtered_at abatido_em DATETIME DEFAULT NULL, CHANGE farm_id fazenda_id INT NOT NULL');
        $this->addSql('ALTER TABLE cow ADD CONSTRAINT `FK_99D43F9CD4A3545F` FOREIGN KEY (fazenda_id) REFERENCES farm (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_99D43F9CD4A3545F ON cow (fazenda_id)');
        $this->addSql('ALTER TABLE farm CHANGE responsible responsavel VARCHAR(150) NOT NULL, CHANGE size tamanho NUMERIC(10, 2) NOT NULL');
    }
}
