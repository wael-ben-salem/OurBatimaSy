<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250425224531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE abonnement (id_abonnement INT AUTO_INCREMENT NOT NULL, nom_abonnement VARCHAR(255) DEFAULT NULL, duree VARCHAR(100) DEFAULT NULL, prix NUMERIC(10, 2) DEFAULT NULL, PRIMARY KEY(id_abonnement)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE article (id INT AUTO_INCREMENT NOT NULL, etapeprojet_id INT DEFAULT NULL, stock_id INT NOT NULL, fournisseur_id INT NOT NULL, nom VARCHAR(255) NOT NULL, description VARCHAR(500) DEFAULT NULL, prix_unitaire VARCHAR(50) DEFAULT NULL, photo VARCHAR(255) DEFAULT NULL, INDEX fournisseur_id (fournisseur_id), INDEX etapeprojet_id (etapeprojet_id), INDEX stock_id (stock_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE artisan (artisan_id INT NOT NULL, specialite VARCHAR(255) NOT NULL, salaire_heure NUMERIC(10, 2) NOT NULL, PRIMARY KEY(artisan_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE client (client_id INT NOT NULL, PRIMARY KEY(client_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE constructeur (constructeur_id INT NOT NULL, specialite VARCHAR(100) NOT NULL, salaire_heure NUMERIC(10, 2) NOT NULL, PRIMARY KEY(constructeur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE contrat (id_contrat INT AUTO_INCREMENT NOT NULL, type_contrat VARCHAR(255) NOT NULL, date_signature DATE NOT NULL, date_debut DATE NOT NULL, signature_electronique VARCHAR(500) DEFAULT NULL, date_fin DATE NOT NULL, montant_total INT NOT NULL, Id_projet INT NOT NULL, INDEX IDX_60349993F4D3ABE7 (Id_projet), PRIMARY KEY(id_contrat)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
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
            CREATE TABLE discussion (id INT AUTO_INCREMENT NOT NULL, plannification_id INT DEFAULT NULL, sender_id INT DEFAULT NULL, recipient_id INT DEFAULT NULL, content LONGTEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_C0B9F90F4C2E1DF5 (plannification_id), INDEX IDX_C0B9F90FF624B39D (sender_id), INDEX IDX_C0B9F90FE92F8F78 (recipient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE equipe (id INT AUTO_INCREMENT NOT NULL, constructeur_id INT DEFAULT NULL, gestionnairestock_id INT DEFAULT NULL, nom VARCHAR(100) NOT NULL, date_creation DATE DEFAULT CURRENT_DATE NOT NULL, rating NUMERIC(3, 2) DEFAULT '0.00', INDEX idx_constructeur (constructeur_id), INDEX idx_gestionnaire (gestionnairestock_id), UNIQUE INDEX nom (nom), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE equipe_artisan (equipe_id INT NOT NULL, artisan_id INT NOT NULL, INDEX IDX_E460166F6D861B89 (equipe_id), INDEX IDX_E460166F5ED3C7B7 (artisan_id), PRIMARY KEY(equipe_id, artisan_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE etapeprojet (Id_etapeProjet INT AUTO_INCREMENT NOT NULL, nomEtape VARCHAR(50) NOT NULL, description TEXT NOT NULL, dateDebut DATE DEFAULT NULL, dateFin DATE DEFAULT NULL, statut VARCHAR(255) DEFAULT 'En attente', montant NUMERIC(15, 3) DEFAULT NULL, Id_projet INT NOT NULL, Id_rapport INT DEFAULT NULL, INDEX Id_projet (Id_projet), INDEX Id_rapport (Id_rapport), PRIMARY KEY(Id_etapeProjet)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE fournisseur (fournisseur_id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, numero_de_telephone VARCHAR(50) DEFAULT NULL, adresse VARCHAR(255) DEFAULT NULL, UNIQUE INDEX email (email), PRIMARY KEY(fournisseur_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE gestionnairestock (gestionnairestock_id INT NOT NULL, PRIMARY KEY(gestionnairestock_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
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
            CREATE TABLE planif_notifications (id INT AUTO_INCREMENT NOT NULL, recipient_id INT NOT NULL, plannification_id INT DEFAULT NULL, message LONGTEXT NOT NULL, is_read TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_EB1F00B5E92F8F78 (recipient_id), INDEX IDX_EB1F00B54C2E1DF5 (plannification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE plannification (id_plannification INT AUTO_INCREMENT NOT NULL, id_tache INT DEFAULT NULL, priorite VARCHAR(255) DEFAULT NULL, date_planifiee DATE NOT NULL, heure_debut TIME DEFAULT NULL, heure_fin TIME DEFAULT NULL, remarques TEXT DEFAULT NULL, statut VARCHAR(255) DEFAULT NULL, INDEX id_tache (id_tache), PRIMARY KEY(id_plannification)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE projet (id_client INT DEFAULT NULL, Id_projet INT AUTO_INCREMENT NOT NULL, type VARCHAR(20) NOT NULL, styleArch VARCHAR(20) DEFAULT NULL, budget NUMERIC(15, 3) NOT NULL, etat VARCHAR(20) DEFAULT NULL, dateCreation DATETIME NOT NULL, nomProjet VARCHAR(30) DEFAULT NULL, Id_terrain INT DEFAULT NULL, Id_equipe INT DEFAULT NULL, INDEX id_client (id_client), INDEX Id_terrain (Id_terrain), INDEX Id_equipe (Id_equipe), UNIQUE INDEX nomProjet (nomProjet), PRIMARY KEY(Id_projet)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE rapport (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(100) NOT NULL, contenu TEXT NOT NULL, dateCreation DATE NOT NULL, Id_etapeProjet INT DEFAULT NULL, INDEX Id_etapeProjet (Id_etapeProjet), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
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
            CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, emplacement VARCHAR(255) DEFAULT NULL, dateCreation DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE tache (id_tache INT AUTO_INCREMENT NOT NULL, constructeur_id INT DEFAULT NULL, artisan_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, date_fin DATE DEFAULT NULL, etat VARCHAR(255) DEFAULT NULL, Id_projet INT DEFAULT NULL, INDEX constructeur_id (constructeur_id), INDEX artisan_id (artisan_id), INDEX Id_projet (Id_projet), PRIMARY KEY(id_tache)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE teamrating (id INT AUTO_INCREMENT NOT NULL, client_id INT DEFAULT NULL, team_id INT DEFAULT NULL, rating DOUBLE PRECISION DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, INDEX team_id (team_id), INDEX client_id (client_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE terrain (Id_terrain INT AUTO_INCREMENT NOT NULL, emplacement VARCHAR(100) NOT NULL, caracteristiques TEXT NOT NULL, superficie NUMERIC(10, 2) DEFAULT NULL, detailsGeo VARCHAR(100) DEFAULT NULL, Id_projet INT DEFAULT NULL, Id_visite INT DEFAULT NULL, INDEX Id_projet (Id_projet), INDEX Id_visite (Id_visite), PRIMARY KEY(Id_terrain)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE utilisateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(50) NOT NULL, prenom VARCHAR(50) NOT NULL, email VARCHAR(100) NOT NULL, telephone VARCHAR(20) DEFAULT NULL, role VARCHAR(255) DEFAULT 'Client', adresse TEXT DEFAULT NULL, mot_de_passe VARCHAR(255) NOT NULL, statut VARCHAR(255) DEFAULT 'en_attente', isConfirmed TINYINT(1) DEFAULT NULL, face_data LONGBLOB DEFAULT NULL, reset_token VARCHAR(255) DEFAULT NULL, reset_token_expiry DATETIME DEFAULT NULL, INDEX idx_role (role), UNIQUE INDEX email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE visite (Id_visite INT AUTO_INCREMENT NOT NULL, dateVisite DATE NOT NULL, observations VARCHAR(200) DEFAULT NULL, Id_projet INT DEFAULT NULL, Id_terrain INT DEFAULT NULL, INDEX Id_terrain (Id_terrain), INDEX Id_projet (Id_projet), PRIMARY KEY(Id_visite)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT FK_23A0E663F5BA5ED FOREIGN KEY (etapeprojet_id) REFERENCES etapeprojet (Id_etapeProjet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT FK_23A0E66DCD6110 FOREIGN KEY (stock_id) REFERENCES stock (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article ADD CONSTRAINT FK_23A0E66670C757F FOREIGN KEY (fournisseur_id) REFERENCES fournisseur (fournisseur_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE artisan ADD CONSTRAINT FK_3C600AD35ED3C7B7 FOREIGN KEY (artisan_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client ADD CONSTRAINT FK_C744045519EB6921 FOREIGN KEY (client_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE constructeur ADD CONSTRAINT FK_71A7BD9E8815B605 FOREIGN KEY (constructeur_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat ADD CONSTRAINT FK_60349993F4D3ABE7 FOREIGN KEY (Id_projet) REFERENCES projet (Id_projet)
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
            ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F4C2E1DF5 FOREIGN KEY (plannification_id) REFERENCES plannification (id_plannification)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90FF624B39D FOREIGN KEY (sender_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90FE92F8F78 FOREIGN KEY (recipient_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe ADD CONSTRAINT FK_2449BA158815B605 FOREIGN KEY (constructeur_id) REFERENCES constructeur (constructeur_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe ADD CONSTRAINT FK_2449BA15F5D7D49E FOREIGN KEY (gestionnairestock_id) REFERENCES gestionnairestock (gestionnairestock_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan ADD CONSTRAINT FK_E460166F6D861B89 FOREIGN KEY (equipe_id) REFERENCES equipe (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan ADD CONSTRAINT FK_E460166F5ED3C7B7 FOREIGN KEY (artisan_id) REFERENCES artisan (artisan_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet ADD CONSTRAINT FK_6AB7F44BF4D3ABE7 FOREIGN KEY (Id_projet) REFERENCES projet (Id_projet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet ADD CONSTRAINT FK_6AB7F44B2FF40A3C FOREIGN KEY (Id_rapport) REFERENCES rapport (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestionnairestock ADD CONSTRAINT FK_9287C31F5D7D49E FOREIGN KEY (gestionnairestock_id) REFERENCES utilisateur (id)
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
            ALTER TABLE planif_notifications ADD CONSTRAINT FK_EB1F00B5E92F8F78 FOREIGN KEY (recipient_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE planif_notifications ADD CONSTRAINT FK_EB1F00B54C2E1DF5 FOREIGN KEY (plannification_id) REFERENCES plannification (id_plannification)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE plannification ADD CONSTRAINT FK_E88A48127D026145 FOREIGN KEY (id_tache) REFERENCES tache (id_tache)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet ADD CONSTRAINT FK_50159CA959B6F911 FOREIGN KEY (Id_terrain) REFERENCES terrain (Id_terrain)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet ADD CONSTRAINT FK_50159CA9808F8D5B FOREIGN KEY (Id_equipe) REFERENCES equipe (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet ADD CONSTRAINT FK_50159CA9E173B1B8 FOREIGN KEY (id_client) REFERENCES client (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rapport ADD CONSTRAINT FK_BE34A09CB86D4452 FOREIGN KEY (Id_etapeProjet) REFERENCES etapeprojet (Id_etapeProjet)
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
            ALTER TABLE tache ADD CONSTRAINT FK_938720758815B605 FOREIGN KEY (constructeur_id) REFERENCES constructeur (constructeur_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache ADD CONSTRAINT FK_938720755ED3C7B7 FOREIGN KEY (artisan_id) REFERENCES artisan (artisan_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache ADD CONSTRAINT FK_93872075F4D3ABE7 FOREIGN KEY (Id_projet) REFERENCES projet (Id_projet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE teamrating ADD CONSTRAINT FK_95B783FE19EB6921 FOREIGN KEY (client_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE teamrating ADD CONSTRAINT FK_95B783FE296CD8AE FOREIGN KEY (team_id) REFERENCES equipe (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain ADD CONSTRAINT FK_C87653B1F4D3ABE7 FOREIGN KEY (Id_projet) REFERENCES projet (Id_projet)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain ADD CONSTRAINT FK_C87653B1145ABBF5 FOREIGN KEY (Id_visite) REFERENCES visite (Id_visite)
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
            ALTER TABLE article DROP FOREIGN KEY FK_23A0E663F5BA5ED
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP FOREIGN KEY FK_23A0E66DCD6110
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE article DROP FOREIGN KEY FK_23A0E66670C757F
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE artisan DROP FOREIGN KEY FK_3C600AD35ED3C7B7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE client DROP FOREIGN KEY FK_C744045519EB6921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE constructeur DROP FOREIGN KEY FK_71A7BD9E8815B605
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP FOREIGN KEY FK_60349993F4D3ABE7
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
            ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F4C2E1DF5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90FF624B39D
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90FE92F8F78
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA158815B605
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe DROP FOREIGN KEY FK_2449BA15F5D7D49E
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan DROP FOREIGN KEY FK_E460166F6D861B89
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE equipe_artisan DROP FOREIGN KEY FK_E460166F5ED3C7B7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet DROP FOREIGN KEY FK_6AB7F44BF4D3ABE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet DROP FOREIGN KEY FK_6AB7F44B2FF40A3C
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE gestionnairestock DROP FOREIGN KEY FK_9287C31F5D7D49E
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
            ALTER TABLE planif_notifications DROP FOREIGN KEY FK_EB1F00B5E92F8F78
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE planif_notifications DROP FOREIGN KEY FK_EB1F00B54C2E1DF5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE plannification DROP FOREIGN KEY FK_E88A48127D026145
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet DROP FOREIGN KEY FK_50159CA959B6F911
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9808F8D5B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet DROP FOREIGN KEY FK_50159CA9E173B1B8
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE rapport DROP FOREIGN KEY FK_BE34A09CB86D4452
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
            ALTER TABLE tache DROP FOREIGN KEY FK_938720758815B605
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache DROP FOREIGN KEY FK_938720755ED3C7B7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE tache DROP FOREIGN KEY FK_93872075F4D3ABE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE teamrating DROP FOREIGN KEY FK_95B783FE19EB6921
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE teamrating DROP FOREIGN KEY FK_95B783FE296CD8AE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain DROP FOREIGN KEY FK_C87653B1F4D3ABE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE terrain DROP FOREIGN KEY FK_C87653B1145ABBF5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visite DROP FOREIGN KEY FK_B09C8CBBF4D3ABE7
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE visite DROP FOREIGN KEY FK_B09C8CBB59B6F911
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE abonnement
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE article
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE artisan
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE client
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE constructeur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE contrat
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
            DROP TABLE discussion
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE equipe
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE equipe_artisan
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE etapeprojet
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE fournisseur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE gestionnairestock
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
            DROP TABLE planif_notifications
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE plannification
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE projet
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE rapport
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
            DROP TABLE stock
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE tache
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE teamrating
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE terrain
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE utilisateur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE visite
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}
