<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240508115940 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE seminar_participant ADD web_meeting_url_token VARCHAR(64) DEFAULT NULL, DROP web_meeting_url');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE seminar_participant ADD web_meeting_url VARCHAR(100) CHARACTER SET utf8mb3 DEFAULT NULL COLLATE `utf8mb3_unicode_ci`, DROP web_meeting_url_token');
    }
}
