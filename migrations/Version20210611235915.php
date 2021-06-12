<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210611235915 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification CHANGE date_notif date_notif DATETIME DEFAULT NULL, CHANGE action action VARCHAR(255) DEFAULT NULL, CHANGE contenu contenu VARCHAR(255) DEFAULT NULL, CHANGE id_proj id_proj INT DEFAULT NULL, CHANGE id_team id_team INT DEFAULT NULL, CHANGE owner_proj owner_proj VARCHAR(255) DEFAULT NULL, CHANGE name_proj name_proj VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification CHANGE date_notif date_notif DATETIME NOT NULL, CHANGE action action VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE contenu contenu VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE owner_proj owner_proj VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE name_proj name_proj VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE id_proj id_proj INT NOT NULL, CHANGE id_team id_team INT NOT NULL');
    }
}
