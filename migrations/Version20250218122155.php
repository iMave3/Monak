<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250218122155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company_information CHANGE company_name company_name VARCHAR(255) NOT NULL, CHANGE ico ico INT NOT NULL, CHANGE dic dic VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE order_set ADD order_summary_id INT NOT NULL');
        $this->addSql('ALTER TABLE order_set ADD CONSTRAINT FK_A54F6ABC99182909 FOREIGN KEY (order_summary_id) REFERENCES order_summary (id)');
        $this->addSql('CREATE INDEX IDX_A54F6ABC99182909 ON order_set (order_summary_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE company_information CHANGE company_name company_name VARCHAR(255) DEFAULT NULL, CHANGE ico ico INT DEFAULT NULL, CHANGE dic dic VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE order_set DROP FOREIGN KEY FK_A54F6ABC99182909');
        $this->addSql('DROP INDEX IDX_A54F6ABC99182909 ON order_set');
        $this->addSql('ALTER TABLE order_set DROP order_summary_id');
    }
}
