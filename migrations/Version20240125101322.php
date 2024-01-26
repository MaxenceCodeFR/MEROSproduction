<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240125101322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_company ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contact_company ADD CONSTRAINT FK_D59028CEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_D59028CEA76ED395 ON contact_company (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_company DROP FOREIGN KEY FK_D59028CEA76ED395');
        $this->addSql('DROP INDEX IDX_D59028CEA76ED395 ON contact_company');
        $this->addSql('ALTER TABLE contact_company DROP user_id');
    }
}
