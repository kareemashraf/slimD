<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180727144548 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE emaillist (id INT AUTO_INCREMENT NOT NULL, list_name VARCHAR(30) NOT NULL, user_id VARCHAR(5) NOT NULL, file VARCHAR(50) NOT NULL, data_added DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8067C1A48C9F3610 (file), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE history (id INT AUTO_INCREMENT NOT NULL, list_id INT DEFAULT NULL, user_id VARCHAR(5) NOT NULL, fromtext TINYTEXT NOT NULL, sendername TINYTEXT DEFAULT NULL, subjecttext TINYTEXT NOT NULL, message_html LONGTEXT NOT NULL, message_plaintext LONGTEXT NOT NULL, order_date DATETIME NOT NULL, is_active TINYINT(1) NOT NULL, INDEX IDX_27BA704B3DAE168B (list_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE leads (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(254) DEFAULT NULL, name VARCHAR(25) DEFAULT NULL, gender VARCHAR(10) DEFAULT NULL, list_id VARCHAR(25) NOT NULL, sent TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(25) NOT NULL, fullname VARCHAR(64) NOT NULL, phone INT NOT NULL, about TINYTEXT DEFAULT NULL, password VARCHAR(64) NOT NULL, email VARCHAR(60) NOT NULL, is_active TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9F85E0677 (username), UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE history ADD CONSTRAINT FK_27BA704B3DAE168B FOREIGN KEY (list_id) REFERENCES emaillist (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE history DROP FOREIGN KEY FK_27BA704B3DAE168B');
        $this->addSql('DROP TABLE emaillist');
        $this->addSql('DROP TABLE history');
        $this->addSql('DROP TABLE leads');
        $this->addSql('DROP TABLE users');
    }
}
