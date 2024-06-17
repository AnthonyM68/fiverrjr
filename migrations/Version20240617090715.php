<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240617090715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE service_course (service_id INT NOT NULL, course_id INT NOT NULL, INDEX IDX_28596A8FED5CA9E6 (service_id), INDEX IDX_28596A8F591CC992 (course_id), PRIMARY KEY(service_id, course_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(50) NOT NULL, last_name VARCHAR(50) NOT NULL, phone_number VARCHAR(50) NOT NULL, date_register DATETIME NOT NULL, picture VARCHAR(255) DEFAULT NULL, city VARCHAR(100) NOT NULL, portfolio VARCHAR(255) DEFAULT NULL, bio LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_course ADD CONSTRAINT FK_28596A8FED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_course ADD CONSTRAINT FK_28596A8F591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE category ADD course_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE category ADD CONSTRAINT FK_64C19C1591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('CREATE INDEX IDX_64C19C1591CC992 ON category (course_id)');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_service_course');
        $this->addSql('DROP INDEX FK_service_course ON service');
        $this->addSql('ALTER TABLE service DROP course_id, DROP title, DROP description, DROP price, DROP duration, DROP create_date');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE service_course DROP FOREIGN KEY FK_28596A8FED5CA9E6');
        $this->addSql('ALTER TABLE service_course DROP FOREIGN KEY FK_28596A8F591CC992');
        $this->addSql('DROP TABLE service_course');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE category DROP FOREIGN KEY FK_64C19C1591CC992');
        $this->addSql('DROP INDEX IDX_64C19C1591CC992 ON category');
        $this->addSql('ALTER TABLE category DROP course_id');
        $this->addSql('ALTER TABLE service ADD course_id INT NOT NULL, ADD title VARCHAR(255) NOT NULL, ADD description LONGTEXT NOT NULL, ADD price NUMERIC(2, 0) NOT NULL, ADD duration INT NOT NULL, ADD create_date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_service_course FOREIGN KEY (course_id) REFERENCES course (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX FK_service_course ON service (course_id)');
    }
}
