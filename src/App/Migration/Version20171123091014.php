<?php declare(strict_types = 1);

namespace App\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171123091014 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, modifier_id INT DEFAULT NULL, date_created DATETIME DEFAULT NULL, date_modified DATETIME DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, firstname VARCHAR(255) DEFAULT NULL, lastname VARCHAR(255) DEFAULT NULL, email VARCHAR(191) DEFAULT NULL, language VARCHAR(2) DEFAULT \'fr\' NOT NULL, photo VARCHAR(255) NOT NULL, is_administrator TINYINT(1) NOT NULL, description LONGTEXT NOT NULL, sex SMALLINT DEFAULT 0 NOT NULL, newsletter TINYINT(1) NOT NULL, phone VARCHAR(25) DEFAULT NULL, skype VARCHAR(255) DEFAULT NULL, profession VARCHAR(255) DEFAULT NULL, ministry VARCHAR(255) DEFAULT NULL, last_login DATETIME DEFAULT NULL, first_login DATETIME DEFAULT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_8D93D64961220EA6 (creator_id), INDEX IDX_8D93D649D079F553 (modifier_id), UNIQUE INDEX user_email (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64961220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D079F553 FOREIGN KEY (modifier_id) REFERENCES user (id)');
    }

    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64961220EA6');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D079F553');
        $this->addSql('DROP TABLE user');
    }
}
