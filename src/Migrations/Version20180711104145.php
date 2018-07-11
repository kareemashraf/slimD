<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180711104145 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE emaillist CHANGE file file VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE history ADD sendername TINYTEXT NOT NULL');
        $this->addSql('ALTER TABLE users CHANGE email email VARCHAR(60) NOT NULL');
        $this->addSql('ALTER TABLE users RENAME INDEX uniq_c2502824f85e0677 TO UNIQ_1483A5E9F85E0677');
        $this->addSql('ALTER TABLE users RENAME INDEX uniq_c2502824e7927c74 TO UNIQ_1483A5E9E7927C74');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE emaillist CHANGE file file VARCHAR(250) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE history DROP sendername');
        $this->addSql('ALTER TABLE users CHANGE email email VARCHAR(254) NOT NULL COLLATE utf8mb4_unicode_ci');
        $this->addSql('ALTER TABLE users RENAME INDEX uniq_1483a5e9f85e0677 TO UNIQ_C2502824F85E0677');
        $this->addSql('ALTER TABLE users RENAME INDEX uniq_1483a5e9e7927c74 TO UNIQ_C2502824E7927C74');
    }
}
