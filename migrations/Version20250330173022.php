<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250330173022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE discussion (id INT AUTO_INCREMENT NOT NULL, planning_id INT NOT NULL, participant1_id INT NOT NULL, participant2_id INT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_C0B9F90F3D865311 (planning_id), INDEX IDX_C0B9F90FB29A9963 (participant1_id), INDEX IDX_C0B9F90FA02F368D (participant2_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE message (id INT AUTO_INCREMENT NOT NULL, discussion_id INT NOT NULL, sender_id INT NOT NULL, content LONGTEXT NOT NULL, sent_at DATETIME NOT NULL, INDEX IDX_B6BD307F1ADED311 (discussion_id), INDEX IDX_B6BD307FF624B39D (sender_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90F3D865311 FOREIGN KEY (planning_id) REFERENCES planning (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90FB29A9963 FOREIGN KEY (participant1_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE discussion ADD CONSTRAINT FK_C0B9F90FA02F368D FOREIGN KEY (participant2_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F1ADED311 FOREIGN KEY (discussion_id) REFERENCES discussion (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90F3D865311');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90FB29A9963');
        $this->addSql('ALTER TABLE discussion DROP FOREIGN KEY FK_C0B9F90FA02F368D');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307F1ADED311');
        $this->addSql('ALTER TABLE message DROP FOREIGN KEY FK_B6BD307FF624B39D');
        $this->addSql('DROP TABLE discussion');
        $this->addSql('DROP TABLE message');
    }
}
