<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

//versionamento do banco (criação e edição de tabelas)
final class Version20260520200917 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Criação das tabelas: cow, farm, farm_veterinarian, veterinarian';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cow (id INT AUTO_INCREMENT NOT NULL, codigo VARCHAR(50) NOT NULL, litros_leite_por_semana NUMERIC(8, 2) NOT NULL, racao_por_semana NUMERIC(8, 2) NOT NULL, peso NUMERIC(8, 2) NOT NULL, data_nascimento DATE NOT NULL, abatido TINYINT NOT NULL, abatido_em DATETIME DEFAULT NULL, fazenda_id INT NOT NULL, INDEX IDX_99D43F9CD4A3545F (fazenda_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE farm (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, responsavel VARCHAR(150) NOT NULL, tamanho NUMERIC(10, 2) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE farm_veterinarian (farm_id INT NOT NULL, veterinarian_id INT NOT NULL, INDEX IDX_499A5CC65FCFA0D (farm_id), INDEX IDX_499A5CC804C8213 (veterinarian_id), PRIMARY KEY (farm_id, veterinarian_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE veterinarian (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, crmv VARCHAR(20) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE cow ADD CONSTRAINT FK_99D43F9CD4A3545F FOREIGN KEY (fazenda_id) REFERENCES farm (id)');
        $this->addSql('ALTER TABLE farm_veterinarian ADD CONSTRAINT FK_499A5CC65FCFA0D FOREIGN KEY (farm_id) REFERENCES farm (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE farm_veterinarian ADD CONSTRAINT FK_499A5CC804C8213 FOREIGN KEY (veterinarian_id) REFERENCES veterinarian (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cow DROP FOREIGN KEY FK_99D43F9CD4A3545F');
        $this->addSql('ALTER TABLE farm_veterinarian DROP FOREIGN KEY FK_499A5CC65FCFA0D');
        $this->addSql('ALTER TABLE farm_veterinarian DROP FOREIGN KEY FK_499A5CC804C8213');
        $this->addSql('DROP TABLE cow');
        $this->addSql('DROP TABLE farm');
        $this->addSql('DROP TABLE farm_veterinarian');
        $this->addSql('DROP TABLE veterinarian');
        $this->addSql('DROP TABLE messenger_messages');
    }
}