<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250420170316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE discussion (id INT AUTO_INCREMENT NOT NULL, plannification_id INT DEFAULT NULL, sender_id INT DEFAULT NULL, recipient_id INT DEFAULT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_C0B9F90F4C2E1DF5 (plannification_id), INDEX IDX_C0B9F90FF624B39D (sender_id), INDEX IDX_C0B9F90FE92F8F78 (recipient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE etapeprojet (Id_etapeProjet INT AUTO_INCREMENT NOT NULL, nomEtape VARCHAR(50) NOT NULL, description TEXT NOT NULL, dateDebut DATE DEFAULT NULL, dateFin DATE DEFAULT NULL, statut VARCHAR(255) DEFAULT 'En attente', montant NUMERIC(15, 3) DEFAULT NULL, Id_projet INT NOT NULL, Id_rapport INT DEFAULT NULL, INDEX Id_projet (Id_projet), INDEX Id_rapport (Id_rapport), PRIMARY KEY(Id_etapeProjet)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F4C2E1DF5 FOREIGN KEY (plannification_id) REFERENCES plannification (id_plannification)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90FF624B39D FOREIGN KEY (sender_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet ADD CONSTRAINT FK_6AB7F44BF4D3ABE7 FOREIGN KEY (Id_projet) REFERENCES projet (Id_projet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet ADD CONSTRAINT FK_6AB7F44B2FF40A3C FOREIGN KEY (Id_rapport) REFERENCES rapport (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article CHANGE stock_id stock_id INT NOT NULL, CHANGE fournisseur_id fournisseur_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT FK_23A0E663F5BA5ED FOREIGN KEY (etapeprojet_id) REFERENCES etapeprojet (Id_etapeProjet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP FOREIGN KEY FK_6034999319EB6921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP FOREIGN KEY FK_603499938815B605
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX client_id ON contrat
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX constructeur_id ON contrat
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP FOREIGN KEY FK_60349993F4D3ABE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP constructeur_id, DROP client_id, CHANGE date_signature date_signature DATE NOT NULL, CHANGE date_debut date_debut DATE NOT NULL, CHANGE date_fin date_fin DATE NOT NULL, CHANGE montant_total montant_total INT NOT NULL, CHANGE Id_projet Id_projet INT NOT NULL
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
            ALTER TABLE projet CHANGE nomProjet nomProjet VARCHAR(30) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rapport ADD CONSTRAINT FK_BE34A09CB86D4452 FOREIGN KEY (Id_etapeProjet) REFERENCES etapeprojet (Id_etapeProjet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock CHANGE dateCreation dateCreation DATETIME NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP FOREIGN KEY FK_23A0E663F5BA5ED
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rapport DROP FOREIGN KEY FK_BE34A09CB86D4452
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles JSON NOT NULL COMMENT '(DC2Type:json)', password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F4C2E1DF5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90FF624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90FE92F8F78
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet DROP FOREIGN KEY FK_6AB7F44BF4D3ABE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet DROP FOREIGN KEY FK_6AB7F44B2FF40A3C
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE discussion
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE etapeprojet
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article CHANGE stock_id stock_id INT DEFAULT NULL, CHANGE fournisseur_id fournisseur_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP FOREIGN KEY FK_60349993F4D3ABE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat ADD constructeur_id INT DEFAULT NULL, ADD client_id INT DEFAULT NULL, CHANGE date_signature date_signature DATE DEFAULT NULL, CHANGE date_debut date_debut DATE DEFAULT NULL, CHANGE date_fin date_fin DATE DEFAULT NULL, CHANGE montant_total montant_total INT DEFAULT NULL, CHANGE Id_projet Id_projet INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat ADD CONSTRAINT FK_6034999319EB6921 FOREIGN KEY (client_id) REFERENCES client (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat ADD CONSTRAINT FK_603499938815B605 FOREIGN KEY (constructeur_id) REFERENCES constructeur (constructeur_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX client_id ON contrat (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX constructeur_id ON contrat (constructeur_id)
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
            ALTER TABLE projet CHANGE nomProjet nomProjet VARCHAR(30) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock CHANGE dateCreation dateCreation VARCHAR(20) NOT NULL
        SQL);
    }
}
