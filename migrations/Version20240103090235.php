<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240103090235 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact_company (id INT AUTO_INCREMENT NOT NULL, motif_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, company VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, text LONGTEXT NOT NULL, INDEX IDX_D59028CED0EEB819 (motif_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contact_company ADD CONSTRAINT FK_D59028CED0EEB819 FOREIGN KEY (motif_id) REFERENCES motif (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_company DROP FOREIGN KEY FK_D59028CED0EEB819');
        $this->addSql('DROP TABLE contact_company');
    }
}
