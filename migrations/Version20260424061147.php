<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260424061147 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE block (id INT AUTO_INCREMENT NOT NULL, content JSON NOT NULL, visible TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, position INT NOT NULL, page_id INT NOT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_831B9722C4663E4 (page_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB ROW_FORMAT=DYNAMIC');
        $this->addSql('CREATE TABLE page (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, slug VARCHAR(191) NOT NULL, depth INT NOT NULL, published TINYINT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, parent_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_140AB620989D9B62 (slug), INDEX IDX_140AB620727ACA70 (parent_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB ROW_FORMAT=DYNAMIC');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB ROW_FORMAT=DYNAMIC');
        $this->addSql('ALTER TABLE block ADD CONSTRAINT FK_831B9722C4663E4 FOREIGN KEY (page_id) REFERENCES page (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE page ADD CONSTRAINT FK_140AB620727ACA70 FOREIGN KEY (parent_id) REFERENCES page (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE block DROP FOREIGN KEY FK_831B9722C4663E4');
        $this->addSql('ALTER TABLE page DROP FOREIGN KEY FK_140AB620727ACA70');
        $this->addSql('DROP TABLE block');
        $this->addSql('DROP TABLE page');
        $this->addSql('DROP TABLE `user`');
    }
}
