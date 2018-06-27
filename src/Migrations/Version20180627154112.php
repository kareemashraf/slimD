<?php declare(strict_types = 1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180627154112 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE history ADD fromtext VARCHAR(30) NOT NULL, ADD subjecttext VARCHAR(30) NOT NULL, DROP `from`, DROP subject, CHANGE message_text message_plaintext TINYTEXT NOT NULL');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE history ADD `from` VARCHAR(30) NOT NULL COLLATE utf8mb4_unicode_ci, ADD subject VARCHAR(30) NOT NULL COLLATE utf8mb4_unicode_ci, DROP fromtext, DROP subjecttext, CHANGE message_plaintext message_text TINYTEXT NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
