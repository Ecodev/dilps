<?php

declare(strict_types=1);

namespace Application\Migration;

use Doctrine\DBAL\Schema\Schema;

class Version20171214140126 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE dating (id INT AUTO_INCREMENT NOT NULL, creator_id INT DEFAULT NULL, updater_id INT DEFAULT NULL, image_id INT DEFAULT NULL, creation_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', update_date DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', `from` INT NOT NULL, `to` INT NOT NULL, INDEX IDX_D06C7A461220EA6 (creator_id), INDEX IDX_D06C7A4E37ECFB0 (updater_id), INDEX IDX_D06C7A43DA5256D (image_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dating ADD CONSTRAINT FK_D06C7A461220EA6 FOREIGN KEY (creator_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dating ADD CONSTRAINT FK_D06C7A4E37ECFB0 FOREIGN KEY (updater_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE dating ADD CONSTRAINT FK_D06C7A43DA5256D FOREIGN KEY (image_id) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE collection ADD sorting INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE image ADD file_size INT NOT NULL, ADD width INT NOT NULL, ADD height INT NOT NULL, DROP dating_from, DROP dating_to');
    }
}
