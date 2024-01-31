<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240131084228 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_influencer ADD notification_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contact_influencer ADD CONSTRAINT FK_FA26D534EF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id)');
        $this->addSql('CREATE INDEX IDX_FA26D534EF1A9D84 ON contact_influencer (notification_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact_influencer DROP FOREIGN KEY FK_FA26D534EF1A9D84');
        $this->addSql('DROP INDEX IDX_FA26D534EF1A9D84 ON contact_influencer');
        $this->addSql('ALTER TABLE contact_influencer DROP notification_id');
    }
}
