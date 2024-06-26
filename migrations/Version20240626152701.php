<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240626152701 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1AEF5A6C1');
        $this->addSql('DROP INDEX IDX_64C19C1AEF5A6C1 ON category');
        $this->addSql('ALTER TABLE category DROP services_id');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD212469DE2');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD259027487');
        $this->addSql('DROP INDEX IDX_E19D9AD212469DE2 ON service');
        $this->addSql('DROP INDEX IDX_E19D9AD259027487 ON service');
        $this->addSql('ALTER TABLE service DROP category_id, DROP theme_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD services_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1AEF5A6C1 FOREIGN KEY (services_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_64C19C1AEF5A6C1 ON category (services_id)');
        $this->addSql('ALTER TABLE service ADD category_id INT NOT NULL, ADD theme_id INT NOT NULL');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD212469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD259027487 FOREIGN KEY (theme_id) REFERENCES theme (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_E19D9AD212469DE2 ON service (category_id)');
        $this->addSql('CREATE INDEX IDX_E19D9AD259027487 ON service (theme_id)');
    }
}
