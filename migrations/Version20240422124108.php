<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240422124108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE impressions DROP FOREIGN KEY FK_A74C17A0F4297814');
        $this->addSql('ALTER TABLE publications DROP FOREIGN KEY FK_32783AF43256915B');
        $this->addSql('ALTER TABLE publications DROP FOREIGN KEY FK_32783AF4C5D011DA');
        $this->addSql('ALTER TABLE publications DROP FOREIGN KEY FK_32783AF4CCAA542F');
        $this->addSql('ALTER TABLE publications DROP FOREIGN KEY FK_32783AF4F4297814');
        $this->addSql('DROP TABLE analytics');
        $this->addSql('DROP TABLE impressions');
        $this->addSql('DROP TABLE publications');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE analytics (id INT AUTO_INCREMENT NOT NULL, count INT DEFAULT NULL, date DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE impressions (id INT AUTO_INCREMENT NOT NULL, analytics_id INT DEFAULT NULL, likes INT DEFAULT NULL, comments INT DEFAULT NULL, shares INT DEFAULT NULL, INDEX IDX_A74C17A0F4297814 (analytics_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE publications (id INT AUTO_INCREMENT NOT NULL, relation_id INT DEFAULT NULL, plateform_id INT DEFAULT NULL, analytics_id INT DEFAULT NULL, impressions_id INT DEFAULT NULL, image VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, description LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, date DATETIME NOT NULL, INDEX IDX_32783AF43256915B (relation_id), INDEX IDX_32783AF4C5D011DA (impressions_id), INDEX IDX_32783AF4CCAA542F (plateform_id), INDEX IDX_32783AF4F4297814 (analytics_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE impressions ADD CONSTRAINT FK_A74C17A0F4297814 FOREIGN KEY (analytics_id) REFERENCES analytics (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE publications ADD CONSTRAINT FK_32783AF43256915B FOREIGN KEY (relation_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE publications ADD CONSTRAINT FK_32783AF4C5D011DA FOREIGN KEY (impressions_id) REFERENCES impressions (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE publications ADD CONSTRAINT FK_32783AF4CCAA542F FOREIGN KEY (plateform_id) REFERENCES plateform (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE publications ADD CONSTRAINT FK_32783AF4F4297814 FOREIGN KEY (analytics_id) REFERENCES analytics (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
