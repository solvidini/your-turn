<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190518210604 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE task ADD next_id INT DEFAULT NULL, DROP next');
        $this->addSql('ALTER TABLE task ADD CONSTRAINT FK_527EDB25AA23F6C8 FOREIGN KEY (next_id) REFERENCES users (id)');
        $this->addSql('CREATE INDEX IDX_527EDB25AA23F6C8 ON task (next_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE task DROP FOREIGN KEY FK_527EDB25AA23F6C8');
        $this->addSql('DROP INDEX IDX_527EDB25AA23F6C8 ON task');
        $this->addSql('ALTER TABLE task ADD next INT NOT NULL, DROP next_id');
    }
}
