<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240130083952 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, is_new TINYINT(1) NOT NULL, is_seen TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE contact_company ADD notification_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contact_company ADD CONSTRAINT FK_D59028CEEF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id)');
        $this->addSql('CREATE INDEX IDX_D59028CEEF1A9D84 ON contact_company (notification_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_company DROP FOREIGN KEY FK_D59028CEEF1A9D84');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP INDEX IDX_D59028CEEF1A9D84 ON contact_company');
        $this->addSql('ALTER TABLE contact_company DROP notification_id');
    }
}
