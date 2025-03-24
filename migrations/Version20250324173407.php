<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250324173407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_saved_plannings (user_id INT NOT NULL, planning_id INT NOT NULL, INDEX IDX_680324EFA76ED395 (user_id), INDEX IDX_680324EF3D865311 (planning_id), PRIMARY KEY(user_id, planning_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_saved_plannings ADD CONSTRAINT FK_680324EFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_saved_plannings ADD CONSTRAINT FK_680324EF3D865311 FOREIGN KEY (planning_id) REFERENCES planning (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_saved_plannings DROP FOREIGN KEY FK_680324EFA76ED395');
        $this->addSql('ALTER TABLE user_saved_plannings DROP FOREIGN KEY FK_680324EF3D865311');
        $this->addSql('DROP TABLE user_saved_plannings');
    }
}
