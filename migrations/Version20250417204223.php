<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250417204223 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE conversation (id INT AUTO_INCREMENT NOT NULL, equipe_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, date_creation DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX equipe_id (equipe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE conversation_membre (conversation_id INT NOT NULL, utilisateur_id INT NOT NULL, INDEX IDX_C47841E99AC0396 (conversation_id), INDEX IDX_C47841E9FB88E14F (utilisateur_id), PRIMARY KEY(conversation_id, utilisateur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE direct_messages (message_id INT AUTO_INCREMENT NOT NULL, receiver_id INT DEFAULT NULL, sender_id INT DEFAULT NULL, content TEXT NOT NULL, sent_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, is_read TINYINT(1) NOT NULL, INDEX fk_dm_receiver_idx (receiver_id), INDEX idx_messages_sent (sent_at), INDEX fk_dm_sender_idx (sender_id), PRIMARY KEY(message_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, conversation_id INT DEFAULT NULL, expediteur_id INT DEFAULT NULL, contenu TEXT DEFAULT NULL, date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP, lu TINYINT(1) DEFAULT NULL, INDEX conversation_id (conversation_id), INDEX expediteur_id (expediteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messaging_accounts (user_id INT NOT NULL, role_specific_id INT DEFAULT NULL COMMENT 'ID spécifique au rôle (artisan_id, constructeur_id, etc.)', created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, username VARCHAR(255) DEFAULT NULL, INDEX idx_role_specific (role_specific_id), PRIMARY KEY(user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE notifications (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, message TEXT NOT NULL, is_read TINYINT(1) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, read_at DATETIME DEFAULT NULL, type VARCHAR(255) DEFAULT NULL, reference_id INT DEFAULT NULL, INDEX idx_notif_created (created_at), INDEX fk_notification_user_idx (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE reponse_reclamation (reponse_id INT NOT NULL, reclamation_id INT NOT NULL, INDEX IDX_C7CB5101CF18BB82 (reponse_id), INDEX IDX_C7CB51012D6BA2D9 (reclamation_id), PRIMARY KEY(reponse_id, reclamation_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE teamrating (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, team_id INT DEFAULT NULL, rating DOUBLE PRECISION DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX team_id (team_id), INDEX client_id (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation ADD CONSTRAINT FK_8A8E26E96D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation_membre ADD CONSTRAINT FK_C47841E99AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation_membre ADD CONSTRAINT FK_C47841E9FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE direct_messages ADD CONSTRAINT FK_721C1B5ACD53EDB6 FOREIGN KEY (receiver_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE direct_messages ADD CONSTRAINT FK_721C1B5AF624B39D FOREIGN KEY (sender_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F9AC0396 FOREIGN KEY (conversation_id) REFERENCES conversation (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message ADD CONSTRAINT FK_B6BD307F10335F61 FOREIGN KEY (expediteur_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messaging_accounts ADD CONSTRAINT FK_F18E4DDFA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notifications ADD CONSTRAINT FK_6000B0D3A76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse_reclamation ADD CONSTRAINT FK_C7CB5101CF18BB82 FOREIGN KEY (reponse_id) REFERENCES reponse (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse_reclamation ADD CONSTRAINT FK_C7CB51012D6BA2D9 FOREIGN KEY (reclamation_id) REFERENCES reclamation (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE teamrating ADD CONSTRAINT FK_95B783FE19EB6921 FOREIGN KEY (client_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE teamrating ADD CONSTRAINT FK_95B783FE296CD8AE FOREIGN KEY (team_id) REFERENCES equipe (id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP FOREIGN KEY article_ibfk_3
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX Id_etapeProjet ON article
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article CHANGE id id INT AUTO_INCREMENT NOT NULL, CHANGE Id_etapeProjet etapeprojet_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT FK_23A0E663F5BA5ED FOREIGN KEY (etapeprojet_id) REFERENCES etapeprojet (Id_etapeProjet)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX etapeprojet_id ON article (etapeprojet_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE artisan DROP FOREIGN KEY artisan_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE artisan ADD CONSTRAINT FK_3C600AD35ED3C7B7 FOREIGN KEY (artisan_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client DROP FOREIGN KEY client_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client ADD CONSTRAINT FK_C744045519EB6921 FOREIGN KEY (client_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE constructeur DROP FOREIGN KEY constructeur_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE constructeur ADD CONSTRAINT FK_71A7BD9E8815B605 FOREIGN KEY (constructeur_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat ADD CONSTRAINT FK_603499938815B605 FOREIGN KEY (constructeur_id) REFERENCES constructeur (constructeur_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat ADD CONSTRAINT FK_6034999319EB6921 FOREIGN KEY (client_id) REFERENCES client (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe DROP FOREIGN KEY equipe_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX uc_team_members ON equipe
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe ADD rating NUMERIC(3, 2) DEFAULT '0.00', CHANGE constructeur_id constructeur_id INT DEFAULT NULL, CHANGE gestionnairestock_id gestionnairestock_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe ADD CONSTRAINT FK_2449BA158815B605 FOREIGN KEY (constructeur_id) REFERENCES constructeur (constructeur_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan DROP FOREIGN KEY equipe_artisan_ibfk_2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan DROP FOREIGN KEY equipe_artisan_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan DROP FOREIGN KEY equipe_artisan_ibfk_2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan DROP date_integration
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan ADD CONSTRAINT FK_E460166F6D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan ADD CONSTRAINT FK_E460166F5ED3C7B7 FOREIGN KEY (artisan_id) REFERENCES artisan (artisan_id)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_artisan ON equipe_artisan
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E460166F5ED3C7B7 ON equipe_artisan (artisan_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan ADD CONSTRAINT equipe_artisan_ibfk_2 FOREIGN KEY (artisan_id) REFERENCES artisan (artisan_id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet DROP FOREIGN KEY etapeprojet_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet DROP FOREIGN KEY etapeprojet_ibfk_2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet CHANGE Id_projet Id_projet INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet ADD CONSTRAINT FK_6AB7F44BF4D3ABE7 FOREIGN KEY (Id_projet) REFERENCES projet (Id_projet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet ADD CONSTRAINT FK_6AB7F44B2FF40A3C FOREIGN KEY (Id_rapport) REFERENCES rapport (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fournisseur CHANGE fournisseur_id fournisseur_id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestionnairestock DROP FOREIGN KEY gestionnairestock_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestionnairestock ADD CONSTRAINT FK_9287C31F5D7D49E FOREIGN KEY (gestionnairestock_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE plannification DROP FOREIGN KEY plannification_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE plannification CHANGE priorite priorite VARCHAR(255) DEFAULT NULL, CHANGE statut statut VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE plannification ADD CONSTRAINT FK_E88A48127D026145 FOREIGN KEY (id_tache) REFERENCES tache (id_tache)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet DROP FOREIGN KEY projet_ibfk_2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet DROP FOREIGN KEY projet_ibfk_3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet ADD nomProjet VARCHAR(30) NOT NULL, CHANGE dateCreation dateCreation DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet ADD CONSTRAINT FK_50159CA959B6F911 FOREIGN KEY (Id_terrain) REFERENCES terrain (Id_terrain)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet ADD CONSTRAINT FK_50159CA9E173B1B8 FOREIGN KEY (id_client) REFERENCES client (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX nomProjet ON projet (nomProjet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rapport DROP FOREIGN KEY rapport_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rapport ADD CONSTRAINT FK_BE34A09CB86D4452 FOREIGN KEY (Id_etapeProjet) REFERENCES etapeprojet (Id_etapeProjet)
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX Utilisateur ON reclamation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation CHANGE statut statut VARCHAR(255) NOT NULL, CHANGE date date DATE NOT NULL, CHANGE Utilisateur_id id_utilisateur_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation ADD CONSTRAINT FK_CE606404C6EE5C49 FOREIGN KEY (id_utilisateur_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_CE606404C6EE5C49 ON reclamation (id_utilisateur_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse DROP FOREIGN KEY fk_reclamation
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX fk_reclamation ON reponse
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse DROP id_Reclamation, CHANGE statut statut VARCHAR(255) NOT NULL, CHANGE date date DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock CHANGE id id INT AUTO_INCREMENT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache DROP FOREIGN KEY tache_ibfk_3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache DROP FOREIGN KEY tache_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache DROP FOREIGN KEY tache_ibfk_2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache CHANGE Id_projet Id_projet INT DEFAULT NULL, CHANGE etat etat VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache ADD CONSTRAINT FK_938720758815B605 FOREIGN KEY (constructeur_id) REFERENCES constructeur (constructeur_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache ADD CONSTRAINT FK_938720755ED3C7B7 FOREIGN KEY (artisan_id) REFERENCES artisan (artisan_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache ADD CONSTRAINT FK_93872075F4D3ABE7 FOREIGN KEY (Id_projet) REFERENCES projet (Id_projet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain DROP FOREIGN KEY terrain_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain DROP FOREIGN KEY terrain_ibfk_2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain ADD CONSTRAINT FK_C87653B1F4D3ABE7 FOREIGN KEY (Id_projet) REFERENCES projet (Id_projet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain ADD CONSTRAINT FK_C87653B1145ABBF5 FOREIGN KEY (Id_visite) REFERENCES visite (Id_visite)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur ADD face_data LONGBLOB DEFAULT NULL, ADD reset_token VARCHAR(255) DEFAULT NULL, ADD reset_token_expiry DATETIME DEFAULT NULL, CHANGE isConfirmed isConfirmed TINYINT(1) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visite DROP FOREIGN KEY visite_ibfk_1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visite DROP FOREIGN KEY visite_ibfk_2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visite ADD CONSTRAINT FK_B09C8CBBF4D3ABE7 FOREIGN KEY (Id_projet) REFERENCES projet (Id_projet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visite ADD CONSTRAINT FK_B09C8CBB59B6F911 FOREIGN KEY (Id_terrain) REFERENCES terrain (Id_terrain)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles JSON NOT NULL COMMENT '(DC2Type:json)', password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation DROP FOREIGN KEY FK_8A8E26E96D861B89
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation_membre DROP FOREIGN KEY FK_C47841E99AC0396
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE conversation_membre DROP FOREIGN KEY FK_C47841E9FB88E14F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE direct_messages DROP FOREIGN KEY FK_721C1B5ACD53EDB6
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE direct_messages DROP FOREIGN KEY FK_721C1B5AF624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F9AC0396
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F10335F61
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE messaging_accounts DROP FOREIGN KEY FK_F18E4DDFA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notifications DROP FOREIGN KEY FK_6000B0D3A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse_reclamation DROP FOREIGN KEY FK_C7CB5101CF18BB82
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse_reclamation DROP FOREIGN KEY FK_C7CB51012D6BA2D9
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE teamrating DROP FOREIGN KEY FK_95B783FE19EB6921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE teamrating DROP FOREIGN KEY FK_95B783FE296CD8AE
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE conversation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE conversation_membre
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE direct_messages
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE message
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messaging_accounts
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE notifications
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE reponse_reclamation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE teamrating
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP FOREIGN KEY FK_23A0E663F5BA5ED
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX etapeprojet_id ON article
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article CHANGE id id INT NOT NULL, CHANGE etapeprojet_id Id_etapeProjet INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT article_ibfk_3 FOREIGN KEY (Id_etapeProjet) REFERENCES etapeprojet (Id_etapeProjet)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX Id_etapeProjet ON article (Id_etapeProjet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE artisan DROP FOREIGN KEY FK_3C600AD35ED3C7B7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE artisan ADD CONSTRAINT artisan_ibfk_1 FOREIGN KEY (artisan_id) REFERENCES utilisateur (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client DROP FOREIGN KEY FK_C744045519EB6921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client ADD CONSTRAINT client_ibfk_1 FOREIGN KEY (client_id) REFERENCES utilisateur (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE constructeur DROP FOREIGN KEY FK_71A7BD9E8815B605
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE constructeur ADD CONSTRAINT constructeur_ibfk_1 FOREIGN KEY (constructeur_id) REFERENCES utilisateur (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP FOREIGN KEY FK_603499938815B605
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP FOREIGN KEY FK_6034999319EB6921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA158815B605
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe DROP rating, CHANGE constructeur_id constructeur_id INT NOT NULL, CHANGE gestionnairestock_id gestionnairestock_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe ADD CONSTRAINT equipe_ibfk_1 FOREIGN KEY (constructeur_id) REFERENCES constructeur (constructeur_id) ON UPDATE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX uc_team_members ON equipe (constructeur_id, gestionnairestock_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan DROP FOREIGN KEY FK_E460166F6D861B89
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan DROP FOREIGN KEY FK_E460166F5ED3C7B7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan DROP FOREIGN KEY FK_E460166F5ED3C7B7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan ADD date_integration DATE DEFAULT CURRENT_DATE NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan ADD CONSTRAINT equipe_artisan_ibfk_2 FOREIGN KEY (artisan_id) REFERENCES artisan (artisan_id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan ADD CONSTRAINT equipe_artisan_ibfk_1 FOREIGN KEY (equipe_id) REFERENCES equipe (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_e460166f5ed3c7b7 ON equipe_artisan
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_artisan ON equipe_artisan (artisan_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan ADD CONSTRAINT FK_E460166F5ED3C7B7 FOREIGN KEY (artisan_id) REFERENCES artisan (artisan_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet DROP FOREIGN KEY FK_6AB7F44BF4D3ABE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet DROP FOREIGN KEY FK_6AB7F44B2FF40A3C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet CHANGE Id_projet Id_projet INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet ADD CONSTRAINT etapeprojet_ibfk_1 FOREIGN KEY (Id_projet) REFERENCES projet (Id_projet) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet ADD CONSTRAINT etapeprojet_ibfk_2 FOREIGN KEY (Id_rapport) REFERENCES rapport (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE fournisseur CHANGE fournisseur_id fournisseur_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestionnairestock DROP FOREIGN KEY FK_9287C31F5D7D49E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestionnairestock ADD CONSTRAINT gestionnairestock_ibfk_1 FOREIGN KEY (gestionnairestock_id) REFERENCES utilisateur (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE plannification DROP FOREIGN KEY FK_E88A48127D026145
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE plannification CHANGE priorite priorite VARCHAR(255) DEFAULT 'Moyenne', CHANGE statut statut VARCHAR(255) DEFAULT 'Planifié'
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE plannification ADD CONSTRAINT plannification_ibfk_1 FOREIGN KEY (id_tache) REFERENCES tache (id_tache) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet DROP FOREIGN KEY FK_50159CA959B6F911
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9E173B1B8
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX nomProjet ON projet
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet DROP nomProjet, CHANGE dateCreation dateCreation DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet ADD CONSTRAINT projet_ibfk_2 FOREIGN KEY (id_client) REFERENCES client (client_id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet ADD CONSTRAINT projet_ibfk_3 FOREIGN KEY (Id_terrain) REFERENCES terrain (Id_terrain) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rapport DROP FOREIGN KEY FK_BE34A09CB86D4452
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rapport ADD CONSTRAINT rapport_ibfk_1 FOREIGN KEY (Id_etapeProjet) REFERENCES etapeprojet (Id_etapeProjet) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation DROP FOREIGN KEY FK_CE606404C6EE5C49
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_CE606404C6EE5C49 ON reclamation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reclamation CHANGE statut statut VARCHAR(50) NOT NULL, CHANGE date date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, CHANGE id_utilisateur_id Utilisateur_id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX Utilisateur ON reclamation (Utilisateur_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse ADD id_Reclamation INT NOT NULL, CHANGE statut statut VARCHAR(50) NOT NULL, CHANGE date date DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reponse ADD CONSTRAINT fk_reclamation FOREIGN KEY (id_Reclamation) REFERENCES reclamation (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX fk_reclamation ON reponse (id_Reclamation)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE stock CHANGE id id INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache DROP FOREIGN KEY FK_938720758815B605
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache DROP FOREIGN KEY FK_938720755ED3C7B7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache DROP FOREIGN KEY FK_93872075F4D3ABE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache CHANGE etat etat VARCHAR(255) DEFAULT 'En attente', CHANGE Id_projet Id_projet INT NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache ADD CONSTRAINT tache_ibfk_3 FOREIGN KEY (artisan_id) REFERENCES artisan (artisan_id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache ADD CONSTRAINT tache_ibfk_1 FOREIGN KEY (Id_projet) REFERENCES projet (Id_projet) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache ADD CONSTRAINT tache_ibfk_2 FOREIGN KEY (constructeur_id) REFERENCES constructeur (constructeur_id) ON DELETE SET NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain DROP FOREIGN KEY FK_C87653B1F4D3ABE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain DROP FOREIGN KEY FK_C87653B1145ABBF5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain ADD CONSTRAINT terrain_ibfk_1 FOREIGN KEY (Id_projet) REFERENCES projet (Id_projet) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain ADD CONSTRAINT terrain_ibfk_2 FOREIGN KEY (Id_visite) REFERENCES visite (Id_visite) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE utilisateur DROP face_data, DROP reset_token, DROP reset_token_expiry, CHANGE isConfirmed isConfirmed TINYINT(1) DEFAULT 0
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visite DROP FOREIGN KEY FK_B09C8CBBF4D3ABE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visite DROP FOREIGN KEY FK_B09C8CBB59B6F911
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visite ADD CONSTRAINT visite_ibfk_1 FOREIGN KEY (Id_projet) REFERENCES projet (Id_projet) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visite ADD CONSTRAINT visite_ibfk_2 FOREIGN KEY (Id_terrain) REFERENCES terrain (Id_terrain) ON DELETE CASCADE
        SQL);
    }
}
