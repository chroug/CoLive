<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260105155910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE announcePicture (id_photoAnnonce INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, id_annonce INT NOT NULL, INDEX IDX_3C37127528C83A95 (id_annonce), PRIMARY KEY (id_photoAnnonce)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE `like` (id INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, id_annonce INT NOT NULL, INDEX IDX_AC6340B350EAE44 (id_utilisateur), INDEX IDX_AC6340B328C83A95 (id_annonce), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE review (id_avis INT AUTO_INCREMENT NOT NULL, note INT NOT NULL, commentaire LONGTEXT DEFAULT NULL, date_creation DATETIME NOT NULL, id_utilisateur INT NOT NULL, id_annonce INT NOT NULL, INDEX IDX_794381C650EAE44 (id_utilisateur), INDEX IDX_794381C628C83A95 (id_annonce), PRIMARY KEY (id_avis)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id_utilisateur INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, email VARCHAR(100) NOT NULL, tel VARCHAR(50) DEFAULT NULL, mot_de_passe VARCHAR(255) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, date_creation_compte DATETIME NOT NULL, role INT NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id_utilisateur)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE announcePicture ADD CONSTRAINT FK_3C37127528C83A95 FOREIGN KEY (id_annonce) REFERENCES announce (id_annonce)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B350EAE44 FOREIGN KEY (id_utilisateur) REFERENCES user (id_utilisateur)');
        $this->addSql('ALTER TABLE `like` ADD CONSTRAINT FK_AC6340B328C83A95 FOREIGN KEY (id_annonce) REFERENCES announce (id_annonce)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C650EAE44 FOREIGN KEY (id_utilisateur) REFERENCES user (id_utilisateur)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C628C83A95 FOREIGN KEY (id_annonce) REFERENCES announce (id_annonce)');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY `FK_8F91ABF028C83A95`');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY `FK_8F91ABF050EAE44`');
        $this->addSql('ALTER TABLE liker DROP FOREIGN KEY `FK_3ECD7EEB28C83A95`');
        $this->addSql('ALTER TABLE liker DROP FOREIGN KEY `FK_3ECD7EEB50EAE44`');
        $this->addSql('ALTER TABLE photo_annonce DROP FOREIGN KEY `FK_C3B5846828C83A95`');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE liker');
        $this->addSql('DROP TABLE photo_annonce');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('ALTER TABLE announce DROP FOREIGN KEY `FK_F65593E550EAE44`');
        $this->addSql('ALTER TABLE announce ADD CONSTRAINT FK_E6D6DD7550EAE44 FOREIGN KEY (id_utilisateur) REFERENCES user (id_utilisateur)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE avis (id_avis INT AUTO_INCREMENT NOT NULL, note INT NOT NULL, commentaire LONGTEXT CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, date_creation DATETIME NOT NULL, id_utilisateur INT NOT NULL, id_annonce INT NOT NULL, INDEX IDX_8F91ABF028C83A95 (id_annonce), INDEX IDX_8F91ABF050EAE44 (id_utilisateur), PRIMARY KEY (id_avis)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE liker (id INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, id_annonce INT NOT NULL, INDEX IDX_3ECD7EEB50EAE44 (id_utilisateur), INDEX IDX_3ECD7EEB28C83A95 (id_annonce), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE photo_annonce (id_photoAnnonce INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, date_creation DATETIME NOT NULL, id_annonce INT NOT NULL, INDEX IDX_C3B5846828C83A95 (id_annonce), PRIMARY KEY (id_photoAnnonce)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE utilisateur (id_utilisateur INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, prenom VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, email VARCHAR(100) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, tel VARCHAR(50) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, mot_de_passe VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_general_ci`, avatar VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_general_ci`, date_creation_compte DATETIME NOT NULL, role INT NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), PRIMARY KEY (id_utilisateur)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_general_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT `FK_8F91ABF028C83A95` FOREIGN KEY (id_annonce) REFERENCES announce (id_annonce)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT `FK_8F91ABF050EAE44` FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE liker ADD CONSTRAINT `FK_3ECD7EEB28C83A95` FOREIGN KEY (id_annonce) REFERENCES announce (id_annonce)');
        $this->addSql('ALTER TABLE liker ADD CONSTRAINT `FK_3ECD7EEB50EAE44` FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE photo_annonce ADD CONSTRAINT `FK_C3B5846828C83A95` FOREIGN KEY (id_annonce) REFERENCES announce (id_annonce)');
        $this->addSql('ALTER TABLE announcePicture DROP FOREIGN KEY FK_3C37127528C83A95');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B350EAE44');
        $this->addSql('ALTER TABLE `like` DROP FOREIGN KEY FK_AC6340B328C83A95');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C650EAE44');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C628C83A95');
        $this->addSql('DROP TABLE announcePicture');
        $this->addSql('DROP TABLE `like`');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE announce DROP FOREIGN KEY FK_E6D6DD7550EAE44');
        $this->addSql('ALTER TABLE announce ADD CONSTRAINT `FK_F65593E550EAE44` FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
    }
}
