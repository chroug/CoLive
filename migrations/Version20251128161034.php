<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251128161034 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE announce (id_annonce INT AUTO_INCREMENT NOT NULL, titre VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, nb_pieces INT NOT NULL, prix DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, equipements VARCHAR(255) DEFAULT NULL, regle VARCHAR(255) DEFAULT NULL, date_creation DATETIME NOT NULL, disponibilite_debut DATE NOT NULL, disponibilite_fin DATE NOT NULL, adresse VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, id_utilisateur INT NOT NULL, INDEX IDX_F65593E550EAE44 (id_utilisateur), PRIMARY KEY (id_annonce)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE avis (id_avis INT AUTO_INCREMENT NOT NULL, note INT NOT NULL, commentaire LONGTEXT DEFAULT NULL, date_creation DATETIME NOT NULL, id_utilisateur INT NOT NULL, id_annonce INT NOT NULL, INDEX IDX_8F91ABF050EAE44 (id_utilisateur), INDEX IDX_8F91ABF028C83A95 (id_annonce), PRIMARY KEY (id_avis)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE liker (id INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, id_annonce INT NOT NULL, INDEX IDX_3ECD7EEB50EAE44 (id_utilisateur), INDEX IDX_3ECD7EEB28C83A95 (id_annonce), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE photo_annonce (id_photoAnnonce INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) NOT NULL, date_creation DATETIME NOT NULL, id_annonce INT NOT NULL, INDEX IDX_C3B5846828C83A95 (id_annonce), PRIMARY KEY (id_photoAnnonce)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE utilisateur (id_utilisateur INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, email VARCHAR(100) NOT NULL, tel VARCHAR(50) DEFAULT NULL, mot_de_passe VARCHAR(255) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, date_creation_compte DATETIME NOT NULL, role INT NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), PRIMARY KEY (id_utilisateur)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE announce ADD CONSTRAINT FK_F65593E550EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF050EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF028C83A95 FOREIGN KEY (id_annonce) REFERENCES announce (id_annonce)');
        $this->addSql('ALTER TABLE liker ADD CONSTRAINT FK_3ECD7EEB50EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
        $this->addSql('ALTER TABLE liker ADD CONSTRAINT FK_3ECD7EEB28C83A95 FOREIGN KEY (id_annonce) REFERENCES announce (id_annonce)');
        $this->addSql('ALTER TABLE photo_annonce ADD CONSTRAINT FK_C3B5846828C83A95 FOREIGN KEY (id_annonce) REFERENCES announce (id_annonce)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announce DROP FOREIGN KEY FK_F65593E550EAE44');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF050EAE44');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF028C83A95');
        $this->addSql('ALTER TABLE liker DROP FOREIGN KEY FK_3ECD7EEB50EAE44');
        $this->addSql('ALTER TABLE liker DROP FOREIGN KEY FK_3ECD7EEB28C83A95');
        $this->addSql('ALTER TABLE photo_annonce DROP FOREIGN KEY FK_C3B5846828C83A95');
        $this->addSql('DROP TABLE announce');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE liker');
        $this->addSql('DROP TABLE photo_annonce');
        $this->addSql('DROP TABLE utilisateur');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
