<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180508102422 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_8067C1A4B548B0F ON emaillist');
        $this->addSql('ALTER TABLE emaillist CHANGE path file VARCHAR(250) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8067C1A48C9F3610 ON emaillist (file)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP INDEX UNIQ_8067C1A48C9F3610 ON emaillist');
        $this->addSql('ALTER TABLE emaillist CHANGE file path VARCHAR(250) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8067C1A4B548B0F ON emaillist (path)');
    }
}
