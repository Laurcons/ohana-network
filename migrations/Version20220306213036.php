<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220306213036 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE poll CHANGE questions answers LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article CHANGE title title VARCHAR(512) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE content_md content_md VARCHAR(5120) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE seen_by seen_by LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE lulu_ranking CHANGE value value VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE poll ADD questions LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', DROP answers, CHANGE title title VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description description VARCHAR(2048) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE poll_response CHANGE responses responses LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE site_setting CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE value value VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE tellonym CHANGE message message VARCHAR(5120) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE uuid uuid VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE username username VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE real_name real_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE pronouns pronouns VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE status status VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE nickname nickname VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
