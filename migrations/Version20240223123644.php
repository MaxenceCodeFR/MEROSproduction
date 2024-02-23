<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240223123644 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE promoted_link (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, plateform_id INT DEFAULT NULL, link VARCHAR(255) DEFAULT NULL, INDEX IDX_D64429B5A76ED395 (user_id), INDEX IDX_D64429B5CCAA542F (plateform_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE promoted_link ADD CONSTRAINT FK_D64429B5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE promoted_link ADD CONSTRAINT FK_D64429B5CCAA542F FOREIGN KEY (plateform_id) REFERENCES plateform (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE promoted_link DROP FOREIGN KEY FK_D64429B5A76ED395');
        $this->addSql('ALTER TABLE promoted_link DROP FOREIGN KEY FK_D64429B5CCAA542F');
        $this->addSql('DROP TABLE promoted_link');
    }
}
