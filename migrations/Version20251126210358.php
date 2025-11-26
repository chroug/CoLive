<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251126210358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE utilisateur (id_utilisateur INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, email VARCHAR(100) NOT NULL, tel VARCHAR(50) DEFAULT NULL, mot_de_passe VARCHAR(255) NOT NULL, avatar VARCHAR(255) DEFAULT NULL, date_creation_compte DATETIME NOT NULL, role INT NOT NULL, UNIQUE INDEX UNIQ_1D1C63B3E7927C74 (email), PRIMARY KEY (id_utilisateur)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE utilisateur');
    }
}
