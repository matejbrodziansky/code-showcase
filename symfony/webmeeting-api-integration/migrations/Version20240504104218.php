<?php

declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240504104218 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE seminar_participant ADD web_meeting_confirmed_at DATETIME DEFAULT NULL, ADD web_meeting_confirmation_code VARCHAR(32) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE seminar_participant DROP web_meeting_confirmed_at, DROP web_meeting_confirmation_code');
    }
}
