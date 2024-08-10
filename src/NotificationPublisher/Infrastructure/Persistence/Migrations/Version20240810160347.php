<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Persistence\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240810160347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add retry count value to notification';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification ADD retry_count_value INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification DROP retry_count_value');
    }
}
