<?php

declare(strict_types=1);

namespace Application\Migration;

use Doctrine\DBAL\Schema\Schema;

class Version20180222135934 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `change`
  DROP `status`,
  DROP `response_date`,
  DROP `response`;
');
        $this->addSql('ALTER TABLE `image`  DROP `type`;');

        $this->addSql('CREATE TABLE image_image (image_source INT NOT NULL, image_target INT NOT NULL, INDEX IDX_D27880F6F09721BB (image_source), INDEX IDX_D27880F6E9727134 (image_target), PRIMARY KEY(image_source, image_target)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE image_image ADD CONSTRAINT FK_D27880F6F09721BB FOREIGN KEY (image_source) REFERENCES image (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE image_image ADD CONSTRAINT FK_D27880F6E9727134 FOREIGN KEY (image_target) REFERENCES image (id) ON DELETE CASCADE');
    }
}
