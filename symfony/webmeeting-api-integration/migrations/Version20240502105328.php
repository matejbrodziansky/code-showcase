<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240502105328 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE seminar_date ADD web_meeting_id INT DEFAULT NULL, ADD web_meeting_start DATETIME DEFAULT NULL, ADD web_meeting_end DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE seminar_date DROP web_meeting_id, DROP web_meeting_start, DROP web_meeting_end');
    }
}
