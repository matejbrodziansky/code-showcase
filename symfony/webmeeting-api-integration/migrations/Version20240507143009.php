<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240507143009 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE seminar_date CHANGE web_meeting_start web_meeting_start TIME DEFAULT NULL, CHANGE web_meeting_end web_meeting_end TIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE seminar_date CHANGE web_meeting_start web_meeting_start DATETIME DEFAULT NULL, CHANGE web_meeting_end web_meeting_end DATETIME DEFAULT NULL');
    }
}
