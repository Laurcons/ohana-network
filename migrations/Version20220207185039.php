<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220207185039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lulu_ranking DROP FOREIGN KEY FK_39C57CF7C3EF055B');
        $this->addSql('DROP INDEX IDX_39C57CF7C3EF055B ON lulu_ranking');
        $this->addSql('ALTER TABLE lulu_ranking CHANGE target_id_id target_id INT NOT NULL');
        $this->addSql('ALTER TABLE lulu_ranking ADD CONSTRAINT FK_39C57CF7158E0B66 FOREIGN KEY (target_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_39C57CF7158E0B66 ON lulu_ranking (target_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE lulu_ranking DROP FOREIGN KEY FK_39C57CF7158E0B66');
        $this->addSql('DROP INDEX IDX_39C57CF7158E0B66 ON lulu_ranking');
        $this->addSql('ALTER TABLE lulu_ranking CHANGE value value VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE target_id target_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE lulu_ranking ADD CONSTRAINT FK_39C57CF7C3EF055B FOREIGN KEY (target_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_39C57CF7C3EF055B ON lulu_ranking (target_id_id)');
        $this->addSql('ALTER TABLE site_setting CHANGE name name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE value value VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE uuid uuid VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE username username VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE real_name real_name VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE pronouns pronouns VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE status status VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE nickname nickname VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
