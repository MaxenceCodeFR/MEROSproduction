<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231206151205 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE contact_influencer (id INT AUTO_INCREMENT NOT NULL, motif_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, question VARCHAR(255) NOT NULL, text LONGTEXT DEFAULT NULL, cv VARCHAR(255) DEFAULT NULL, INDEX IDX_FA26D534D0EEB819 (motif_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE motif (id INT AUTO_INCREMENT NOT NULL, motif_influencer VARCHAR(255) NOT NULL, motif_company VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contact_influencer ADD CONSTRAINT FK_FA26D534D0EEB819 FOREIGN KEY (motif_id) REFERENCES motif (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_influencer DROP FOREIGN KEY FK_FA26D534D0EEB819');
        $this->addSql('DROP TABLE contact_influencer');
        $this->addSql('DROP TABLE motif');
    }
}
