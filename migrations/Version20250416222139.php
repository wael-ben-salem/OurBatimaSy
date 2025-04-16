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
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP FOREIGN KEY FK_603499938815B605
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP FOREIGN KEY FK_6034999319EB6921
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX client_id ON contrat
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX constructeur_id ON contrat
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat DROP constructeur_id, DROP client_id
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
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, roles JSON NOT NULL COMMENT '(DC2Type:json)', password VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = '' 
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat ADD constructeur_id INT DEFAULT NULL, ADD client_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat ADD CONSTRAINT FK_603499938815B605 FOREIGN KEY (constructeur_id) REFERENCES constructeur (constructeur_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE contrat ADD CONSTRAINT FK_6034999319EB6921 FOREIGN KEY (client_id) REFERENCES client (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX client_id ON contrat (client_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX constructeur_id ON contrat (constructeur_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE etapeprojet CHANGE Id_projet Id_projet INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE projet CHANGE dateCreation dateCreation DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL
        SQL);
    }
}
