<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240820120457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice ADD amount NUMERIC(10, 2) NOT NULL, ADD tva NUMERIC(5, 2) NOT NULL, ADD date_create DATETIME NOT NULL, ADD status VARCHAR(50) NOT NULL, ADD client_first_name VARCHAR(50) NOT NULL, ADD client_last_name VARCHAR(50) NOT NULL, ADD client_address VARCHAR(255) NOT NULL, ADD client_email VARCHAR(255) NOT NULL, ADD order_traceability JSON DEFAULT NULL, ADD invoice_traceability JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice DROP amount, DROP tva, DROP date_create, DROP status, DROP client_first_name, DROP client_last_name, DROP client_address, DROP client_email, DROP order_traceability, DROP invoice_traceability');
    }
}
