<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260105155024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announce DROP FOREIGN KEY `FK_F65593E550EAE44`');
        $this->addSql('DROP INDEX idx_f65593e550eae44 ON announce');
        $this->addSql('CREATE INDEX IDX_E6D6DD7550EAE44 ON announce (id_utilisateur)');
        $this->addSql('ALTER TABLE announce ADD CONSTRAINT `FK_F65593E550EAE44` FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE announce DROP FOREIGN KEY FK_E6D6DD7550EAE44');
        $this->addSql('DROP INDEX idx_e6d6dd7550eae44 ON announce');
        $this->addSql('CREATE INDEX IDX_F65593E550EAE44 ON announce (id_utilisateur)');
        $this->addSql('ALTER TABLE announce ADD CONSTRAINT FK_E6D6DD7550EAE44 FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id_utilisateur)');
    }
}
