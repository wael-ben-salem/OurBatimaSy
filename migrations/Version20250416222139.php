<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250416222139 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY FK_603499938815B605');
        $this->addSql('ALTER TABLE contrat DROP FOREIGN KEY FK_6034999319EB6921');
        $this->addSql('DROP INDEX client_id ON contrat');
        $this->addSql('DROP INDEX constructeur_id ON contrat');
        $this->addSql('ALTER TABLE contrat DROP constructeur_id, DROP client_id');
        $this->addSql('ALTER TABLE etapeprojet CHANGE Id_projet Id_projet INT NOT NULL');
        $this->addSql('ALTER TABLE projet CHANGE dateCreation dateCreation DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contrat ADD constructeur_id INT DEFAULT NULL, ADD client_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT FK_603499938815B605 FOREIGN KEY (constructeur_id) REFERENCES constructeur (constructeur_id)');
        $this->addSql('ALTER TABLE contrat ADD CONSTRAINT FK_6034999319EB6921 FOREIGN KEY (client_id) REFERENCES client (client_id)');
        $this->addSql('CREATE INDEX client_id ON contrat (client_id)');
        $this->addSql('CREATE INDEX constructeur_id ON contrat (constructeur_id)');
        $this->addSql('ALTER TABLE etapeprojet CHANGE Id_projet Id_projet INT DEFAULT NULL');
        $this->addSql('ALTER TABLE projet CHANGE dateCreation dateCreation DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL');
    }
}