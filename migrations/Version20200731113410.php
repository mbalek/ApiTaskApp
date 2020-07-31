<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200731113410 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, setting_id INT NOT NULL, date DATE NOT NULL, estimated_revenue DOUBLE PRECISION NOT NULL, ad_impressions INT NOT NULL, ad_ecpm NUMERIC(8, 2) NOT NULL, clicks INT NOT NULL, ad_ctr NUMERIC(8, 1) NOT NULL, UNIQUE INDEX UNIQ_C7440455EE35BD72 (setting_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE setting (id INT AUTO_INCREMENT NOT NULL, currency VARCHAR(3) NOT NULL, period_length INT NOT NULL, group_by VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE site (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_694309E419EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, client_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_389B78319EB6921 (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE client ADD CONSTRAINT FK_C7440455EE35BD72 FOREIGN KEY (setting_id) REFERENCES setting (id)');
        $this->addSql('ALTER TABLE site ADD CONSTRAINT FK_694309E419EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B78319EB6921 FOREIGN KEY (client_id) REFERENCES client (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE site DROP FOREIGN KEY FK_694309E419EB6921');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B78319EB6921');
        $this->addSql('ALTER TABLE client DROP FOREIGN KEY FK_C7440455EE35BD72');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE setting');
        $this->addSql('DROP TABLE site');
        $this->addSql('DROP TABLE tag');
    }
}
