<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260404120440 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification ADD user_id INT DEFAULT NULL, ADD user_action_id INT DEFAULT NULL, ADD title VARCHAR(255) NOT NULL, ADD message LONGTEXT NOT NULL, ADD is_read TINYINT(1) NOT NULL, ADD received_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD type VARCHAR(25) NOT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA9DD27DD1 FOREIGN KEY (user_action_id) REFERENCES user_action (id) ON DELETE CASCADE');        $this->addSql('CREATE INDEX IDX_BF5476CAA76ED395 ON notification (user_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_BF5476CA9DD27DD1 ON notification (user_action_id)');
        $this->addSql('ALTER TABLE subscription ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_A3C664D3A76ED395 ON subscription (user_id)');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E99A1887DC');
        $this->addSql('DROP INDEX UNIQ_1483A5E99A1887DC ON users');
        $this->addSql('ALTER TABLE users DROP subscription_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA9DD27DD1');
        $this->addSql('DROP INDEX IDX_BF5476CAA76ED395 ON notification');
        $this->addSql('DROP INDEX UNIQ_BF5476CA9DD27DD1 ON notification');
        $this->addSql('ALTER TABLE notification DROP user_id, DROP user_action_id, DROP title, DROP message, DROP is_read, DROP received_at, DROP type, DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE subscription DROP FOREIGN KEY FK_A3C664D3A76ED395');
        $this->addSql('DROP INDEX UNIQ_A3C664D3A76ED395 ON subscription');
        $this->addSql('ALTER TABLE subscription DROP user_id');
        $this->addSql('ALTER TABLE users ADD subscription_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E99A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E99A1887DC ON users (subscription_id)');
    }
}
