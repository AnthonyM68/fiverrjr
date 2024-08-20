<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240820165209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice ADD order_relation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174429E4EEDD FOREIGN KEY (order_relation_id) REFERENCES `order` (id)');
        $this->addSql('CREATE INDEX IDX_9065174429E4EEDD ON invoice (order_relation_id)');
        $this->addSql('ALTER TABLE payment ADD invoice_relation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D2518F368 FOREIGN KEY (invoice_relation_id) REFERENCES invoice (id)');
        $this->addSql('CREATE INDEX IDX_6D28840D2518F368 ON payment (invoice_relation_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment DROP FOREIGN KEY FK_6D28840D2518F368');
        $this->addSql('DROP INDEX IDX_6D28840D2518F368 ON payment');
        $this->addSql('ALTER TABLE payment DROP invoice_relation_id');
        $this->addSql('ALTER TABLE invoice DROP FOREIGN KEY FK_9065174429E4EEDD');
        $this->addSql('DROP INDEX IDX_9065174429E4EEDD ON invoice');
        $this->addSql('ALTER TABLE invoice DROP order_relation_id');
    }
}
