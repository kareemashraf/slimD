<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180627084614 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE emaillist CHANGE list_name list_name VARCHAR(30) NOT NULL, CHANGE user_id user_id VARCHAR(5) NOT NULL');
        $this->addSql('ALTER TABLE app_users CHANGE about about TINYTEXT DEFAULT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE app_users CHANGE about about TINYTEXT NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE emaillist CHANGE list_name list_name VARCHAR(25) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE user_id user_id VARCHAR(25) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
