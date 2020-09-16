<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190321132813 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE basket (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, UNIQUE INDEX UNIQ_2246507B9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE basket_product (basket_id INT NOT NULL, product_id INT NOT NULL, INDEX IDX_17ED14B41BE1FB52 (basket_id), INDEX IDX_17ED14B44584665A (product_id), PRIMARY KEY(basket_id, product_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE basket_row (product_id INT NOT NULL, user_id INT NOT NULL, quantity INT NOT NULL, INDEX IDX_6A9A4F6B4584665A (product_id), INDEX IDX_6A9A4F6BA76ED395 (user_id), PRIMARY KEY(product_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE eorder (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, total_price NUMERIC(10, 2) NOT NULL, address_line VARCHAR(255) NOT NULL, city VARCHAR(255) NOT NULL, postcode VARCHAR(8) NOT NULL, INDEX IDX_DAF05BCF9395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE order_row (id INT AUTO_INCREMENT NOT NULL, e_order_id INT NOT NULL, product_id INT NOT NULL, price NUMERIC(10, 2) NOT NULL, quantity INT NOT NULL, quantity_price NUMERIC(10, 2) NOT NULL, INDEX IDX_C76BB9BB91129BB4 (e_order_id), INDEX IDX_C76BB9BB4584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE basket ADD CONSTRAINT FK_2246507B9395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE basket_product ADD CONSTRAINT FK_17ED14B41BE1FB52 FOREIGN KEY (basket_id) REFERENCES basket (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE basket_product ADD CONSTRAINT FK_17ED14B44584665A FOREIGN KEY (product_id) REFERENCES product (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE basket_row ADD CONSTRAINT FK_6A9A4F6B4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE basket_row ADD CONSTRAINT FK_6A9A4F6BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE eorder ADD CONSTRAINT FK_DAF05BCF9395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE order_row ADD CONSTRAINT FK_C76BB9BB91129BB4 FOREIGN KEY (e_order_id) REFERENCES eorder (id)');
        $this->addSql('ALTER TABLE order_row ADD CONSTRAINT FK_C76BB9BB4584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE product CHANGE description description VARCHAR(255) NOT NULL, CHANGE image image VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE basket_product DROP FOREIGN KEY FK_17ED14B41BE1FB52');
        $this->addSql('ALTER TABLE order_row DROP FOREIGN KEY FK_C76BB9BB91129BB4');
        $this->addSql('DROP TABLE basket');
        $this->addSql('DROP TABLE basket_product');
        $this->addSql('DROP TABLE basket_row');
        $this->addSql('DROP TABLE eorder');
        $this->addSql('DROP TABLE order_row');
        $this->addSql('ALTER TABLE product CHANGE description description VARCHAR(255) DEFAULT NULL COLLATE utf8mb4_unicode_ci, CHANGE image image LONGTEXT NOT NULL COLLATE utf8mb4_unicode_ci');
    }
}
