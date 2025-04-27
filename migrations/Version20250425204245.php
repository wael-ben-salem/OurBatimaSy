<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250425204245 extends AbstractMigration
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
            CREATE TABLE planif_notifications (id INT AUTO_INCREMENT NOT NULL, recipient_id INT NOT NULL, plannification_id INT DEFAULT NULL, message LONGTEXT NOT NULL, is_read TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_EB1F00B5E92F8F78 (recipient_id), INDEX IDX_EB1F00B54C2E1DF5 (plannification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reclamation (id INT AUTO_INCREMENT NOT NULL, id_utilisateur_id INT NOT NULL, description VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, date DATE NOT NULL, INDEX IDX_CE606404C6EE5C49 (id_utilisateur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reponse (id INT AUTO_INCREMENT NOT NULL, description VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, date DATETIME NOT NULL, id_Reclamation INT NOT NULL, INDEX IDX_5FB6DEC750EE2624 (id_Reclamation), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE saved_plannification (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, plannification_id INT DEFAULT NULL, INDEX IDX_9DDFD32EA76ED395 (user_id), INDEX IDX_9DDFD32E4C2E1DF5 (plannification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
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
            ALTER TABLE planif_notifications ADD CONSTRAINT FK_EB1F00B5E92F8F78 FOREIGN KEY (recipient_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE planif_notifications ADD CONSTRAINT FK_EB1F00B54C2E1DF5 FOREIGN KEY (plannification_id) REFERENCES plannification (id_plannification)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404C6EE5C49 FOREIGN KEY (id_utilisateur_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse ADD CONSTRAINT FK_5FB6DEC750EE2624 FOREIGN KEY (id_Reclamation) REFERENCES reclamation (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE saved_plannification ADD CONSTRAINT FK_9DDFD32EA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE saved_plannification ADD CONSTRAINT FK_9DDFD32E4C2E1DF5 FOREIGN KEY (plannification_id) REFERENCES plannification (id_plannification)
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article CHANGE stock_id stock_id INT NOT NULL, CHANGE fournisseur_id fournisseur_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP FOREIGN KEY FK_60349993F4D3ABE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat CHANGE date_signature date_signature DATE NOT NULL, CHANGE date_debut date_debut DATE NOT NULL, CHANGE date_fin date_fin DATE NOT NULL, CHANGE montant_total montant_total INT NOT NULL, CHANGE Id_projet Id_projet INT NOT NULL
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
            ALTER TABLE projet CHANGE dateCreation dateCreation DATETIME NOT NULL, CHANGE nomProjet nomProjet VARCHAR(30) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock CHANGE dateCreation dateCreation DATETIME NOT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
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
            ALTER TABLE planif_notifications DROP FOREIGN KEY FK_EB1F00B5E92F8F78
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE planif_notifications DROP FOREIGN KEY FK_EB1F00B54C2E1DF5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404C6EE5C49
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse DROP FOREIGN KEY FK_5FB6DEC750EE2624
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE saved_plannification DROP FOREIGN KEY FK_9DDFD32EA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE saved_plannification DROP FOREIGN KEY FK_9DDFD32E4C2E1DF5
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE discussion
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE planif_notifications
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reclamation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reponse
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE saved_plannification
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article CHANGE stock_id stock_id INT DEFAULT NULL, CHANGE fournisseur_id fournisseur_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP FOREIGN KEY FK_60349993F4D3ABE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat CHANGE date_signature date_signature DATE DEFAULT NULL, CHANGE date_debut date_debut DATE DEFAULT NULL, CHANGE date_fin date_fin DATE DEFAULT NULL, CHANGE montant_total montant_total INT DEFAULT NULL, CHANGE Id_projet Id_projet INT DEFAULT NULL
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
            ALTER TABLE projet CHANGE dateCreation dateCreation DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE nomProjet nomProjet VARCHAR(30) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock CHANGE dateCreation dateCreation VARCHAR(20) NOT NULL
        SQL);
    }
}
