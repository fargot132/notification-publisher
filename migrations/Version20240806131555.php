<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240806131555 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add notification table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE notification (id BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', status VARCHAR(20) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', user_id_value BINARY(16) NOT NULL COMMENT \'(DC2Type:uuid)\', email_value VARCHAR(255) NOT NULL, phone_number_value VARCHAR(20) NOT NULL, subject_value VARCHAR(255) NOT NULL, content_value VARCHAR(2000) NOT NULL, INDEX user_id_value_idx (user_id_value), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE notification');
    }
}
