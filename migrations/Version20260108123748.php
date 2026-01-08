<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260108123748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE announce (id_annonce INT AUTO_INCREMENT NOT NULL, titre VARCHAR(100) NOT NULL, description LONGTEXT NOT NULL, type VARCHAR(255) NOT NULL, nb_pieces INT NOT NULL, prix DOUBLE PRECISION NOT NULL, latitude DOUBLE PRECISION NOT NULL, longitude DOUBLE PRECISION NOT NULL, equipements VARCHAR(255) DEFAULT NULL, regle VARCHAR(255) DEFAULT NULL, date_creation DATETIME NOT NULL, disponibilite_debut DATE NOT NULL, disponibilite_fin DATE NOT NULL, adresse VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, code_postal VARCHAR(10) DEFAULT NULL, surface DOUBLE PRECISION DEFAULT NULL, id_utilisateur INT NOT NULL, INDEX IDX_E6D6DD7550EAE44 (id_utilisateur), PRIMARY KEY (id_annonce)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE announce_picture (id_photoAnnonce INT AUTO_INCREMENT NOT NULL, contenu LONGTEXT NOT NULL, date_creation DATETIME NOT NULL, id_annonce INT NOT NULL, INDEX IDX_2FC49EC828C83A95 (id_annonce), PRIMARY KEY (id_photoAnnonce)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, content LONGTEXT DEFAULT NULL, attachment VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, is_read TINYINT NOT NULL, sender_id INT NOT NULL, recipient_id INT NOT NULL, INDEX IDX_B6BD307FF624B39D (sender_id), INDEX IDX_B6BD307FE92F8F78 (recipient_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE review (id_avis INT AUTO_INCREMENT NOT NULL, note INT NOT NULL, commentaire LONGTEXT DEFAULT NULL, date_creation DATETIME NOT NULL, id_utilisateur INT NOT NULL, id_annonce INT NOT NULL, INDEX IDX_794381C650EAE44 (id_utilisateur), INDEX IDX_794381C628C83A95 (id_annonce), PRIMARY KEY (id_avis)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user (id_utilisateur INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, email VARCHAR(100) NOT NULL, tel VARCHAR(50) DEFAULT NULL, mot_de_passe VARCHAR(255) NOT NULL, avatar LONGTEXT DEFAULT NULL, date_creation_compte DATETIME NOT NULL, role INT NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id_utilisateur)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_contacts (user_source_id INT NOT NULL, user_target_id INT NOT NULL, INDEX IDX_D3CDF17395DC9185 (user_source_id), INDEX IDX_D3CDF173156E8682 (user_target_id), PRIMARY KEY (user_source_id, user_target_id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE user_likes (id INT AUTO_INCREMENT NOT NULL, id_utilisateur INT NOT NULL, id_annonce INT NOT NULL, INDEX IDX_AB08B52550EAE44 (id_utilisateur), INDEX IDX_AB08B52528C83A95 (id_annonce), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 (queue_name, available_at, delivered_at, id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE announce ADD CONSTRAINT FK_E6D6DD7550EAE44 FOREIGN KEY (id_utilisateur) REFERENCES user (id_utilisateur)');
        $this->addSql('ALTER TABLE announce_picture ADD CONSTRAINT FK_2FC49EC828C83A95 FOREIGN KEY (id_annonce) REFERENCES announce (id_annonce)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id_utilisateur)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES user (id_utilisateur)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C650EAE44 FOREIGN KEY (id_utilisateur) REFERENCES user (id_utilisateur)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C628C83A95 FOREIGN KEY (id_annonce) REFERENCES announce (id_annonce)');
        $this->addSql('ALTER TABLE user_contacts ADD CONSTRAINT FK_D3CDF17395DC9185 FOREIGN KEY (user_source_id) REFERENCES user (id_utilisateur)');
        $this->addSql('ALTER TABLE user_contacts ADD CONSTRAINT FK_D3CDF173156E8682 FOREIGN KEY (user_target_id) REFERENCES user (id_utilisateur)');
        $this->addSql('ALTER TABLE user_likes ADD CONSTRAINT FK_AB08B52550EAE44 FOREIGN KEY (id_utilisateur) REFERENCES user (id_utilisateur)');
        $this->addSql('ALTER TABLE user_likes ADD CONSTRAINT FK_AB08B52528C83A95 FOREIGN KEY (id_annonce) REFERENCES announce (id_annonce)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announce DROP FOREIGN KEY FK_E6D6DD7550EAE44');
        $this->addSql('ALTER TABLE announce_picture DROP FOREIGN KEY FK_2FC49EC828C83A95');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FE92F8F78');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C650EAE44');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C628C83A95');
        $this->addSql('ALTER TABLE user_contacts DROP FOREIGN KEY FK_D3CDF17395DC9185');
        $this->addSql('ALTER TABLE user_contacts DROP FOREIGN KEY FK_D3CDF173156E8682');
        $this->addSql('ALTER TABLE user_likes DROP FOREIGN KEY FK_AB08B52550EAE44');
        $this->addSql('ALTER TABLE user_likes DROP FOREIGN KEY FK_AB08B52528C83A95');
        $this->addSql('DROP TABLE announce');
        $this->addSql('DROP TABLE announce_picture');
        $this->addSql('DROP TABLE message');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_contacts');
        $this->addSql('DROP TABLE user_likes');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
