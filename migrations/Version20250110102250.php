<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250110102250 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE company_information (id INT AUTO_INCREMENT NOT NULL, company_name VARCHAR(255) DEFAULT NULL, ico INT DEFAULT NULL, dic VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE order_set (id INT AUTO_INCREMENT NOT NULL, quantity INT NOT NULL, product_id INT NOT NULL, INDEX IDX_A54F6ABC4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE order_summary (id INT AUTO_INCREMENT NOT NULL, total_price DOUBLE PRECISION NOT NULL, status VARCHAR(255) DEFAULT NULL, user_information_id INT NOT NULL, company_information_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_3852CF284575EE58 (user_information_id), UNIQUE INDEX UNIQ_3852CF28381EA2 (company_information_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE product (id INT AUTO_INCREMENT NOT NULL, image_url VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, is_available TINYINT(1) NOT NULL, price INT DEFAULT NULL, tag_id INT NOT NULL, INDEX IDX_D34A04ADBAD26311 (tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, image_url VARCHAR(255) NOT NULL, parent_tag_id INT DEFAULT NULL, INDEX IDX_389B783F5C1A0D7 (parent_tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, email VARCHAR(180) NOT NULL, is_verified TINYINT(1) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, last_visit DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE user_information (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, mail VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, town VARCHAR(255) NOT NULL, street VARCHAR(255) NOT NULL, house_number VARCHAR(255) NOT NULL, postcode INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE order_set ADD CONSTRAINT FK_A54F6ABC4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE order_summary ADD CONSTRAINT FK_3852CF284575EE58 FOREIGN KEY (user_information_id) REFERENCES user_information (id)');
        $this->addSql('ALTER TABLE order_summary ADD CONSTRAINT FK_3852CF28381EA2 FOREIGN KEY (company_information_id) REFERENCES company_information (id)');
        $this->addSql('ALTER TABLE product ADD CONSTRAINT FK_D34A04ADBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE tag ADD CONSTRAINT FK_389B783F5C1A0D7 FOREIGN KEY (parent_tag_id) REFERENCES tag (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE order_set DROP FOREIGN KEY FK_A54F6ABC4584665A');
        $this->addSql('ALTER TABLE order_summary DROP FOREIGN KEY FK_3852CF284575EE58');
        $this->addSql('ALTER TABLE order_summary DROP FOREIGN KEY FK_3852CF28381EA2');
        $this->addSql('ALTER TABLE product DROP FOREIGN KEY FK_D34A04ADBAD26311');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE tag DROP FOREIGN KEY FK_389B783F5C1A0D7');
        $this->addSql('DROP TABLE company_information');
        $this->addSql('DROP TABLE order_set');
        $this->addSql('DROP TABLE order_summary');
        $this->addSql('DROP TABLE product');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_information');
    }
}
