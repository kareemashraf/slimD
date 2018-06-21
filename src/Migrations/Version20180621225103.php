<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180621225103 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE leads (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(254) DEFAULT NULL, name VARCHAR(25) DEFAULT NULL, gender VARCHAR(10) DEFAULT NULL, list_id VARCHAR(25) NOT NULL, sent TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE Leadsmin');
        $this->addSql('ALTER TABLE emaillist CHANGE list_name list_name VARCHAR(30) NOT NULL, CHANGE user_id user_id VARCHAR(5) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE Leadsmin (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(254) DEFAULT NULL COLLATE utf8mb4_unicode_ci, name VARCHAR(25) DEFAULT NULL COLLATE utf8mb4_unicode_ci, gender VARCHAR(10) DEFAULT NULL COLLATE utf8mb4_unicode_ci, list_id VARCHAR(25) NOT NULL COLLATE utf8mb4_unicode_ci, sent TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE leads');
        $this->addSql('ALTER TABLE emaillist CHANGE list_name list_name VARCHAR(25) NOT NULL COLLATE utf8mb4_unicode_ci, CHANGE user_id user_id VARCHAR(25) NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
