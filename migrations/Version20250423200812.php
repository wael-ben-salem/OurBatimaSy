<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250423200812 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE planif_notifications (id INT AUTO_INCREMENT NOT NULL, recipient_id INT NOT NULL, plannification_id INT DEFAULT NULL, message LONGTEXT NOT NULL, is_read TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_EB1F00B5E92F8F78 (recipient_id), INDEX IDX_EB1F00B54C2E1DF5 (plannification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE saved_plannification (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, plannification_id INT DEFAULT NULL, INDEX IDX_9DDFD32EA76ED395 (user_id), INDEX IDX_9DDFD32E4C2E1DF5 (plannification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE planif_notifications ADD CONSTRAINT FK_EB1F00B5E92F8F78 FOREIGN KEY (recipient_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE planif_notifications ADD CONSTRAINT FK_EB1F00B54C2E1DF5 FOREIGN KEY (plannification_id) REFERENCES plannification (id_plannification)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE saved_plannification ADD CONSTRAINT FK_9DDFD32EA76ED395 FOREIGN KEY (user_id) REFERENCES utilisateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE saved_plannification ADD CONSTRAINT FK_9DDFD32E4C2E1DF5 FOREIGN KEY (plannification_id) REFERENCES plannification (id_plannification)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE planif_notifications DROP FOREIGN KEY FK_EB1F00B5E92F8F78
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE planif_notifications DROP FOREIGN KEY FK_EB1F00B54C2E1DF5
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE saved_plannification DROP FOREIGN KEY FK_9DDFD32EA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE saved_plannification DROP FOREIGN KEY FK_9DDFD32E4C2E1DF5
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE planif_notifications
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE saved_plannification
        SQL);
    }
}
