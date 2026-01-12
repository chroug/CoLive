<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260112135546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reservation (id_reservation INT AUTO_INCREMENT NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, statut VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL, id_annonce INT NOT NULL, id_utilisateur INT NOT NULL, INDEX IDX_42C8495528C83A95 (id_annonce), INDEX IDX_42C8495550EAE44 (id_utilisateur), PRIMARY KEY (id_reservation)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495528C83A95 FOREIGN KEY (id_annonce) REFERENCES announce (id_annonce)');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495550EAE44 FOREIGN KEY (id_utilisateur) REFERENCES user (id_utilisateur)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495528C83A95');
        $this->addSql('ALTER TABLE reservation DROP FOREIGN KEY FK_42C8495550EAE44');
        $this->addSql('DROP TABLE reservation');
    }
}
