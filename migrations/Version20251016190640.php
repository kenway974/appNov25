<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251016190640 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE plan (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, slug VARCHAR(255) DEFAULT NULL, price DOUBLE PRECISION NOT NULL, duration INT NOT NULL, features LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', stripe_product_id VARCHAR(255) DEFAULT NULL, stripe_price_id VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_need_history DROP FOREIGN KEY FK_1B686EC2A76ED395');
        $this->addSql('ALTER TABLE user_need_history DROP FOREIGN KEY FK_1B686EC23A47EC6E');
        $this->addSql('ALTER TABLE user_subscription DROP FOREIGN KEY FK_230A18D19A1887DC');
        $this->addSql('ALTER TABLE user_subscription DROP FOREIGN KEY FK_230A18D1A76ED395');
        $this->addSql('DROP TABLE user_need_history');
        $this->addSql('DROP TABLE user_subscription');
        $this->addSql('ALTER TABLE subscription ADD plan_id INT NOT NULL, ADD status VARCHAR(25) NOT NULL, ADD start_date DATETIME NOT NULL, ADD end_date DATETIME NOT NULL, ADD renewal_date DATETIME DEFAULT NULL, ADD is_recurring TINYINT(1) NOT NULL, ADD is_active TINYINT(1) NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP description, DROP illustration, DROP stripe_price_id, DROP currency, DROP features, CHANGE price user_id INT NOT NULL, CHANGE title transaction_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3E899029B FOREIGN KEY (plan_id) REFERENCES plan (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A3C664D3A76ED395 ON subscription (user_id)');
        $this->addSql('CREATE INDEX IDX_A3C664D3E899029B ON subscription (plan_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3E899029B');
        $this->addSql('CREATE TABLE user_need_history (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, user_need_id INT NOT NULL, score INT NOT NULL, datetime DATETIME NOT NULL, INDEX IDX_1B686EC23A47EC6E (user_need_id), INDEX IDX_1B686EC2A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE user_subscription (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, subscription_id INT NOT NULL, start_date DATETIME NOT NULL, expire_date DATETIME NOT NULL, is_recurring TINYINT(1) NOT NULL, is_active TINYINT(1) NOT NULL, stripe_subscription_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, stripe_customer_id VARCHAR(255) CHARACTER SET utf8mb4 DEFAULT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_230A18D19A1887DC (subscription_id), UNIQUE INDEX UNIQ_230A18D1A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE user_need_history ADD CONSTRAINT FK_1B686EC2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_need_history ADD CONSTRAINT FK_1B686EC23A47EC6E FOREIGN KEY (user_need_id) REFERENCES user_need (id)');
        $this->addSql('ALTER TABLE user_subscription ADD CONSTRAINT FK_230A18D19A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id)');
        $this->addSql('ALTER TABLE user_subscription ADD CONSTRAINT FK_230A18D1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('DROP TABLE plan');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3A76ED395');
        $this->addSql('DROP INDEX UNIQ_A3C664D3A76ED395 ON subscription');
        $this->addSql('DROP INDEX IDX_A3C664D3E899029B ON subscription');
        $this->addSql('ALTER TABLE subscription ADD description LONGTEXT DEFAULT NULL, ADD price INT NOT NULL, ADD illustration VARCHAR(255) DEFAULT NULL, ADD stripe_price_id VARCHAR(255) DEFAULT NULL, ADD currency VARCHAR(10) DEFAULT NULL, ADD features JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', DROP user_id, DROP plan_id, DROP status, DROP start_date, DROP end_date, DROP renewal_date, DROP is_recurring, DROP is_active, DROP created_at, DROP updated_at, CHANGE transaction_id title VARCHAR(255) NOT NULL');
    }
}
