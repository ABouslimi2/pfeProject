<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210521085745 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mercure_notifications CHANGE name name VARCHAR(255) DEFAULT NULL, CHANGE nb_commits nb_commits INT DEFAULT NULL, CHANGE nb_releases nb_releases INT DEFAULT NULL, CHANGE nb_merges nb_merges INT DEFAULT NULL, CHANGE nb_issues nb_issues INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mercure_notifications CHANGE name name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE nb_commits nb_commits INT NOT NULL, CHANGE nb_releases nb_releases INT NOT NULL, CHANGE nb_merges nb_merges INT NOT NULL, CHANGE nb_issues nb_issues INT NOT NULL');
    }
}
