<?php

declare(strict_types=1);

namespace Application\Migration;

use Doctrine\DBAL\Schema\Schema;

class Version20180306155504 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE `change` DROP FOREIGN KEY FK_4057FE20A41BB822');
        $this->addSql('ALTER TABLE `change` ADD CONSTRAINT FK_4057FE20A41BB822 FOREIGN KEY (suggestion_id) REFERENCES card (id) ON DELETE CASCADE');
    }
}
