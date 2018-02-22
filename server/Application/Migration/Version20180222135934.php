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
    }
}
