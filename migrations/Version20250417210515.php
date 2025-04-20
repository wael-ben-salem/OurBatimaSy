<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250417210515 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
       
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP FOREIGN KEY FK_60349993F4D3ABE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat ADD updated_at DATETIME DEFAULT NULL, CHANGE date_signature date_signature DATE NOT NULL, CHANGE date_debut date_debut DATE NOT NULL, CHANGE date_fin date_fin DATE NOT NULL, CHANGE montant_total montant_total INT NOT NULL, CHANGE Id_projet Id_projet INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX id_projet ON contrat
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_60349993F4D3ABE7 ON contrat (Id_projet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat ADD CONSTRAINT FK_60349993F4D3ABE7 FOREIGN KEY (Id_projet) REFERENCES projet (Id_projet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet CHANGE Id_projet Id_projet INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet CHANGE dateCreation dateCreation DATETIME NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP FOREIGN KEY FK_60349993F4D3ABE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP updated_at, CHANGE date_signature date_signature DATE DEFAULT NULL, CHANGE date_debut date_debut DATE DEFAULT NULL, CHANGE date_fin date_fin DATE DEFAULT NULL, CHANGE montant_total montant_total INT DEFAULT NULL, CHANGE Id_projet Id_projet INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_60349993f4d3abe7 ON contrat
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX Id_projet ON contrat (Id_projet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat ADD CONSTRAINT FK_60349993F4D3ABE7 FOREIGN KEY (Id_projet) REFERENCES projet (Id_projet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet CHANGE Id_projet Id_projet INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet CHANGE dateCreation dateCreation DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL
        SQL);
    }
}
