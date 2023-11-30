<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231130102537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE media (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, images VARCHAR(255) DEFAULT NULL, INDEX IDX_6A2CA10CA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE social (id INT AUTO_INCREMENT NOT NULL, instagram VARCHAR(255) DEFAULT NULL, snapchat VARCHAR(255) DEFAULT NULL, tiktok VARCHAR(255) DEFAULT NULL, twitter VARCHAR(255) DEFAULT NULL, facebook VARCHAR(255) DEFAULT NULL, pinterest VARCHAR(255) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, youtube VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE specialty (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, firstname VARCHAR(255) NOT NULL, lastname VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_social (user_id INT NOT NULL, social_id INT NOT NULL, INDEX IDX_1433FABAA76ED395 (user_id), INDEX IDX_1433FABAFFEB5B27 (social_id), PRIMARY KEY(user_id, social_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_specialty (user_id INT NOT NULL, specialty_id INT NOT NULL, INDEX IDX_E0862B08A76ED395 (user_id), INDEX IDX_E0862B089A353316 (specialty_id), PRIMARY KEY(user_id, specialty_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE media ADD CONSTRAINT FK_6A2CA10CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_social ADD CONSTRAINT FK_1433FABAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_social ADD CONSTRAINT FK_1433FABAFFEB5B27 FOREIGN KEY (social_id) REFERENCES social (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_specialty ADD CONSTRAINT FK_E0862B08A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_specialty ADD CONSTRAINT FK_E0862B089A353316 FOREIGN KEY (specialty_id) REFERENCES specialty (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE media DROP FOREIGN KEY FK_6A2CA10CA76ED395');
        $this->addSql('ALTER TABLE user_social DROP FOREIGN KEY FK_1433FABAA76ED395');
        $this->addSql('ALTER TABLE user_social DROP FOREIGN KEY FK_1433FABAFFEB5B27');
        $this->addSql('ALTER TABLE user_specialty DROP FOREIGN KEY FK_E0862B08A76ED395');
        $this->addSql('ALTER TABLE user_specialty DROP FOREIGN KEY FK_E0862B089A353316');
        $this->addSql('DROP TABLE media');
        $this->addSql('DROP TABLE social');
        $this->addSql('DROP TABLE specialty');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_social');
        $this->addSql('DROP TABLE user_specialty');
    }
}
