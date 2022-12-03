<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221203180620 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE secret_santa_assignation (id INT AUTO_INCREMENT NOT NULL, sender_id INT NOT NULL, receiver_id INT DEFAULT NULL, address VARCHAR(1024) NOT NULL, observations VARCHAR(1024) DEFAULT NULL, custom_status VARCHAR(255) DEFAULT NULL, message VARCHAR(2048) DEFAULT NULL, INDEX IDX_584386ACF624B39D (sender_id), INDEX IDX_584386ACCD53EDB6 (receiver_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE secret_santa_assignation ADD CONSTRAINT FK_584386ACF624B39D FOREIGN KEY (sender_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE secret_santa_assignation ADD CONSTRAINT FK_584386ACCD53EDB6 FOREIGN KEY (receiver_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE secret_santa_assignation');
        $this->addSql('ALTER TABLE article CHANGE title title VARCHAR(512) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE content_md content_md VARCHAR(5120) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE seen_by seen_by LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:array)\'');
        $this->addSql('ALTER TABLE lulu_ranking CHANGE value value VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE notification CHANGE content_md content_md VARCHAR(1024) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE poll CHANGE title title VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE description description VARCHAR(2048) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE answers answers LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', CHANGE options options LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE poll_response CHANGE responses responses LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\'');
        $this->addSql('ALTER TABLE site_setting CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE value value VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE tellonym CHANGE message message VARCHAR(5120) NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE uuid uuid VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE username username VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE real_name real_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE pronouns pronouns VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE status status VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE nickname nickname VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
