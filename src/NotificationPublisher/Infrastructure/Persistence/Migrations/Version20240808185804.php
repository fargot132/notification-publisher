<?php

declare(strict_types=1);

namespace App\NotificationPublisher\Infrastructure\Persistence\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240808185804 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add notification_record table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE notification_record (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', notification_id BINARY(16) DEFAULT NULL COMMENT \'(DC2Type:uuid)\', status VARCHAR(20) NOT NULL, channel VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', message_value VARCHAR(2000) NOT NULL, INDEX IDX_5F9361CCEF1A9D84 (notification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE notification_record ADD CONSTRAINT FK_5F9361CCEF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id)');
        $this->addSql('ALTER TABLE notification ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE notification_record DROP FOREIGN KEY FK_5F9361CCEF1A9D84');
        $this->addSql('DROP TABLE notification_record');
        $this->addSql('ALTER TABLE notification DROP updated_at');
    }
}
