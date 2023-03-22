<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230319085640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL, DROP name, DROP surname, DROP registered, DROP avatar, DROP token, DROP confirm, DROP private, DROP data_update, DROP date_registration, DROP id_rol, CHANGE email email VARCHAR(180) NOT NULL, CHANGE password password VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_8D93D649E7927C74 ON user');
        $this->addSql('ALTER TABLE user ADD name VARCHAR(55) NOT NULL, ADD surname VARCHAR(55) NOT NULL, ADD registered DATETIME DEFAULT NULL, ADD avatar VARCHAR(150) DEFAULT NULL, ADD token VARCHAR(150) DEFAULT NULL, ADD confirm TINYINT(1) DEFAULT NULL, ADD private TINYINT(1) DEFAULT NULL, ADD data_update DATETIME DEFAULT NULL, ADD date_registration DATE NOT NULL, ADD id_rol INT DEFAULT NULL, DROP roles, CHANGE email email VARCHAR(75) NOT NULL, CHANGE password password VARCHAR(500) NOT NULL');
    }
}
