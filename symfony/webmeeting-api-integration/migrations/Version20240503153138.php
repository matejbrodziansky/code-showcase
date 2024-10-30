<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240503153138 extends AbstractMigration
{

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE seminar_participant ADD web_meeting_url VARCHAR(100) DEFAULT NULL, ADD web_meeting_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE seminar_participant DROP web_meeting_url, DROP web_meeting_id');
    }
}
