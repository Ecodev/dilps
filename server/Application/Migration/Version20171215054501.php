<?php

declare(strict_types=1);

namespace Application\Migration;

use Doctrine\DBAL\Schema\Schema;

class Version20171215054501 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE INDEX name ON institution (name)');
        $this->addSql('ALTER TABLE collection ADD institution_id INT DEFAULT NULL, DROP organization');
        $this->addSql('ALTER TABLE collection ADD CONSTRAINT FK_FC4D653210405986 FOREIGN KEY (institution_id) REFERENCES institution (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_FC4D653210405986 ON collection (institution_id)');
        $this->addSql('CREATE INDEX name ON collection (name)');
        $this->addSql('CREATE INDEX name ON image (name)');
        $this->addSql('CREATE INDEX name ON artist (name)');
        $this->addSql('CREATE INDEX name ON tag (name)');
        $this->addSql('ALTER TABLE user ADD institution_id INT DEFAULT NULL, DROP organization');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64910405986 FOREIGN KEY (institution_id) REFERENCES institution (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_8D93D64910405986 ON user (institution_id)');
    }
}
