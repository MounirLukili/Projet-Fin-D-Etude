<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240514230250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE score DROP FOREIGN KEY FK_3299375189D40298');
        $this->addSql('DROP INDEX IDX_3299375189D40298 ON score');
        $this->addSql('ALTER TABLE score ADD niveau VARCHAR(50) NOT NULL, DROP Exercice_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE score ADD Exercice_id INT NOT NULL, DROP niveau');
        $this->addSql('ALTER TABLE score ADD CONSTRAINT FK_3299375189D40298 FOREIGN KEY (Exercice_id) REFERENCES exercice (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_3299375189D40298 ON score (Exercice_id)');
    }
}
