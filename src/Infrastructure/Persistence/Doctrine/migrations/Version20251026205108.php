<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251026205108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE currency (id SERIAL NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE customer (id SERIAL NOT NULL, simple_phone_number VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE domain_config (id SERIAL NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE hotel (id SERIAL NOT NULL, currency_id INT DEFAULT NULL, main_contact_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, address VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3535ED938248176 ON hotel (currency_id)');
        $this->addSql('CREATE INDEX IDX_3535ED9DF595129 ON hotel (main_contact_id)');
        $this->addSql('CREATE TABLE hotel_contact (id SERIAL NOT NULL, hotel_id INT NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9F28F8113243BB18 ON hotel_contact (hotel_id)');
        $this->addSql('CREATE TABLE language (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE reservation (id SERIAL NOT NULL, room_id INT DEFAULT NULL, hotel_id INT DEFAULT NULL, customer_id INT DEFAULT NULL, reference VARCHAR(255) NOT NULL, room_price DOUBLE PRECISION NOT NULL, booked_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, booked_start_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, booked_end_time TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_42C8495554177093 ON reservation (room_id)');
        $this->addSql('CREATE INDEX IDX_42C849553243BB18 ON reservation (hotel_id)');
        $this->addSql('CREATE INDEX IDX_42C849559395C3F3 ON reservation (customer_id)');
        $this->addSql('CREATE TABLE room (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE hotel ADD CONSTRAINT FK_3535ED938248176 FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hotel ADD CONSTRAINT FK_3535ED9DF595129 FOREIGN KEY (main_contact_id) REFERENCES hotel_contact (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE hotel_contact ADD CONSTRAINT FK_9F28F8113243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C8495554177093 FOREIGN KEY (room_id) REFERENCES room (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849553243BB18 FOREIGN KEY (hotel_id) REFERENCES hotel (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reservation ADD CONSTRAINT FK_42C849559395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE hotel DROP CONSTRAINT FK_3535ED938248176');
        $this->addSql('ALTER TABLE hotel DROP CONSTRAINT FK_3535ED9DF595129');
        $this->addSql('ALTER TABLE hotel_contact DROP CONSTRAINT FK_9F28F8113243BB18');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C8495554177093');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C849553243BB18');
        $this->addSql('ALTER TABLE reservation DROP CONSTRAINT FK_42C849559395C3F3');
        $this->addSql('DROP TABLE currency');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE domain_config');
        $this->addSql('DROP TABLE hotel');
        $this->addSql('DROP TABLE hotel_contact');
        $this->addSql('DROP TABLE language');
        $this->addSql('DROP TABLE reservation');
        $this->addSql('DROP TABLE room');
    }
}
