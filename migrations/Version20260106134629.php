<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260106134629 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_likes (id INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, id_annonce INT NOT NULL, INDEX IDX_AB08B52550EAE44 (id_utilisateur), INDEX IDX_AB08B52528C83A95 (id_annonce), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE user_likes ADD CONSTRAINT FK_AB08B52550EAE44 FOREIGN KEY (id_utilisateur) REFERENCES user (id_utilisateur)');
        $this->addSql('ALTER TABLE user_likes ADD CONSTRAINT FK_AB08B52528C83A95 FOREIGN KEY (id_annonce) REFERENCES announce (id_annonce)');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY `FK_AC6340B328C83A95`');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY `FK_AC6340B350EAE44`');
        $this->addSql('DROP TABLE `like`');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, id_annonce INT NOT NULL, INDEX IDX_AC6340B350EAE44 (id_utilisateur), INDEX IDX_AC6340B328C83A95 (id_annonce), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT `FK_AC6340B328C83A95` FOREIGN KEY (id_annonce) REFERENCES announce (id_annonce)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT `FK_AC6340B350EAE44` FOREIGN KEY (id_utilisateur) REFERENCES user (id_utilisateur)');
        $this->addSql('ALTER TABLE user_likes DROP FOREIGN KEY FK_AB08B52550EAE44');
        $this->addSql('ALTER TABLE user_likes DROP FOREIGN KEY FK_AB08B52528C83A95');
        $this->addSql('DROP TABLE user_likes');
    }
}
