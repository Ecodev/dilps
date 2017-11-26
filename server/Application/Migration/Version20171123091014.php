<?php

declare(strict_types=1);

namespace Application\Migration;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20171123091014 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, updater_id INT DEFAULT NULL, date_created DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_updated DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', login VARCHAR(50) NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(191) NOT NULL, organization VARCHAR(255) NOT NULL, is_administrator TINYINT(1) NOT NULL, active_until DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', terms_agreement DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', type ENUM(\'default\', \'unil\') DEFAULT \'default\' NOT NULL COMMENT \'(DC2Type:UserType)\', UNIQUE INDEX UNIQ_8D93D649AA08CB10 (login), INDEX IDX_8D93D64961220EA6 (creator_id), INDEX IDX_8D93D649E37ECFB0 (updater_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64961220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649E37ECFB0 FOREIGN KEY (updater_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        $this->throwIrreversibleMigrationException();
    }
}
