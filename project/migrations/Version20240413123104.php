<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240413123104 extends AbstractMigration
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
        $this->addSql('CREATE TABLE college_details (id INT NOT NULL, college_list_id INT NOT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, website VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_80C88188CF409C89 ON college_details (college_list_id)');
        $this->addSql('CREATE TABLE college_list (id INT NOT NULL, image_url VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, city VARCHAR(100) DEFAULT NULL, state VARCHAR(100) DEFAULT NULL, url VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN college_list.created_at IS \'(DC2Type:datetime_immutable)\'');
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
    }
}
