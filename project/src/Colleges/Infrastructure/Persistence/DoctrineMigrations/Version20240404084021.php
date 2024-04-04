<?php

declare(strict_types=1);

namespace App\Colleges\Infrastructure\Persistence\DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240404084021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE college_details_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE college_list_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE college_details (id INT NOT NULL, college_list_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, phone VARCHAR(50) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_80C88188CF409C89 ON college_details (college_list_id)');
        $this->addSql('CREATE TABLE college_list (id INT NOT NULL, image_url VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, city VARCHAR(100) NOT NULL, state VARCHAR(100) NOT NULL, url VARCHAR(255) NOT NULL, hash VARCHAR(40) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C755B164D1B862B8 ON college_list (hash)');
        $this->addSql('CREATE TABLE ext_translations (id SERIAL NOT NULL, locale VARCHAR(8) NOT NULL, object_class VARCHAR(191) NOT NULL, field VARCHAR(32) NOT NULL, foreign_key VARCHAR(64) NOT NULL, content TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX translations_lookup_idx ON ext_translations (locale, object_class, foreign_key)');
        $this->addSql('CREATE INDEX general_translations_lookup_idx ON ext_translations (object_class, foreign_key)');
        $this->addSql('CREATE UNIQUE INDEX lookup_unique_idx ON ext_translations (locale, object_class, field, foreign_key)');
        $this->addSql('ALTER TABLE college_details ADD CONSTRAINT FK_80C88188CF409C89 FOREIGN KEY (college_list_id) REFERENCES college_list (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE college_details_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE college_list_id_seq CASCADE');
        $this->addSql('ALTER TABLE college_details DROP CONSTRAINT FK_80C88188CF409C89');
        $this->addSql('DROP TABLE college_details');
        $this->addSql('DROP TABLE college_list');
        $this->addSql('DROP TABLE ext_translations');
    }
}
