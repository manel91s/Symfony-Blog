<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230321184849 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD name VARCHAR(50) NOT NULL, ADD surname VARCHAR(50) NOT NULL, ADD avatar VARCHAR(150) DEFAULT NULL, ADD token VARCHAR(150) DEFAULT NULL, ADD confirm TINYINT(1) DEFAULT NULL, ADD private TINYINT(1) DEFAULT NULL, ADD data_update TIME DEFAULT NULL, ADD date_registration TIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP name, DROP surname, DROP avatar, DROP token, DROP confirm, DROP private, DROP data_update, DROP date_registration');
    }
}
