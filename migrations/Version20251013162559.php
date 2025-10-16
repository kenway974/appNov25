<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251013162559 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE action (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, intension VARCHAR(25) DEFAULT NULL, is_doable_now TINYINT(1) DEFAULT NULL, duration INT DEFAULT NULL, type VARCHAR(25) DEFAULT NULL, icon VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE action_block (action_id INT NOT NULL, block_id INT NOT NULL, INDEX IDX_64D55B9E9D32F035 (action_id), INDEX IDX_64D55B9EE9ED820C (block_id), PRIMARY KEY(action_id, block_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE block (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, description LONGTEXT DEFAULT NULL, type VARCHAR(25) DEFAULT NULL, beliefs JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', reframings JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', icon VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feeling (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, emotion VARCHAR(25) DEFAULT NULL, triggers JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', color VARCHAR(25) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feeling_block (feeling_id INT NOT NULL, block_id INT NOT NULL, INDEX IDX_389AAB9DCB9214C2 (feeling_id), INDEX IDX_389AAB9DE9ED820C (block_id), PRIMARY KEY(feeling_id, block_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE feeling_need (feeling_id INT NOT NULL, need_id INT NOT NULL, INDEX IDX_6C8FD0F1CB9214C2 (feeling_id), INDEX IDX_6C8FD0F1624AF264 (need_id), PRIMARY KEY(feeling_id, need_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE need (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, type VARCHAR(25) DEFAULT NULL, fulfilment JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', icon VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE need_action (need_id INT NOT NULL, action_id INT NOT NULL, INDEX IDX_3F2D1D23624AF264 (need_id), INDEX IDX_3F2D1D239D32F035 (action_id), PRIMARY KEY(need_id, action_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, user_action_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, message VARCHAR(255) DEFAULT NULL, type VARCHAR(25) NOT NULL, received_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_read TINYINT(1) NOT NULL, INDEX IDX_BF5476CAA76ED395 (user_id), UNIQUE INDEX UNIQ_BF5476CA9DD27DD1 (user_action_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE subscription (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, price INT NOT NULL, illustration VARCHAR(255) DEFAULT NULL, stripe_price_id VARCHAR(255) DEFAULT NULL, currency VARCHAR(10) DEFAULT NULL, features JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, username VARCHAR(25) NOT NULL, dashboard_illustration VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_verified TINYINT(1) NOT NULL, updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_action (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, action_id INT NOT NULL, user_need_id INT NOT NULL, deadline DATETIME DEFAULT NULL, start_date DATE DEFAULT NULL, frequency INT DEFAULT NULL, completions INT DEFAULT NULL, is_checked TINYINT(1) DEFAULT NULL, is_recurring TINYINT(1) DEFAULT NULL, last_update DATETIME DEFAULT NULL, status VARCHAR(25) NOT NULL, INDEX IDX_229E97AFA76ED395 (user_id), INDEX IDX_229E97AF9D32F035 (action_id), INDEX IDX_229E97AF3A47EC6E (user_need_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_need (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, need_id INT NOT NULL, priority INT NOT NULL, score INT NOT NULL, notes JSON DEFAULT NULL COMMENT \'(DC2Type:json)\', last_updated DATETIME DEFAULT NULL, INDEX IDX_9C75208DA76ED395 (user_id), INDEX IDX_9C75208D624AF264 (need_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_need_history (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, user_need_id INT NOT NULL, score INT NOT NULL, datetime DATETIME NOT NULL, INDEX IDX_1B686EC2A76ED395 (user_id), INDEX IDX_1B686EC23A47EC6E (user_need_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_subscription (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, subscription_id INT NOT NULL, start_date DATETIME NOT NULL, expire_date DATETIME NOT NULL, is_recurring TINYINT(1) NOT NULL, is_active TINYINT(1) NOT NULL, stripe_subscription_id VARCHAR(255) DEFAULT NULL, stripe_customer_id VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_230A18D1A76ED395 (user_id), INDEX IDX_230A18D19A1887DC (subscription_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE action_block ADD CONSTRAINT FK_64D55B9E9D32F035 FOREIGN KEY (action_id) REFERENCES action (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE action_block ADD CONSTRAINT FK_64D55B9EE9ED820C FOREIGN KEY (block_id) REFERENCES block (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feeling_block ADD CONSTRAINT FK_389AAB9DCB9214C2 FOREIGN KEY (feeling_id) REFERENCES feeling (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feeling_block ADD CONSTRAINT FK_389AAB9DE9ED820C FOREIGN KEY (block_id) REFERENCES block (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feeling_need ADD CONSTRAINT FK_6C8FD0F1CB9214C2 FOREIGN KEY (feeling_id) REFERENCES feeling (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE feeling_need ADD CONSTRAINT FK_6C8FD0F1624AF264 FOREIGN KEY (need_id) REFERENCES need (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE need_action ADD CONSTRAINT FK_3F2D1D23624AF264 FOREIGN KEY (need_id) REFERENCES need (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE need_action ADD CONSTRAINT FK_3F2D1D239D32F035 FOREIGN KEY (action_id) REFERENCES action (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE notification ADD CONSTRAINT FK_BF5476CA9DD27DD1 FOREIGN KEY (user_action_id) REFERENCES user_action (id)');
        $this->addSql('ALTER TABLE user_action ADD CONSTRAINT FK_229E97AFA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_action ADD CONSTRAINT FK_229E97AF9D32F035 FOREIGN KEY (action_id) REFERENCES action (id)');
        $this->addSql('ALTER TABLE user_action ADD CONSTRAINT FK_229E97AF3A47EC6E FOREIGN KEY (user_need_id) REFERENCES user_need (id)');
        $this->addSql('ALTER TABLE user_need ADD CONSTRAINT FK_9C75208DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_need ADD CONSTRAINT FK_9C75208D624AF264 FOREIGN KEY (need_id) REFERENCES need (id)');
        $this->addSql('ALTER TABLE user_need_history ADD CONSTRAINT FK_1B686EC2A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_need_history ADD CONSTRAINT FK_1B686EC23A47EC6E FOREIGN KEY (user_need_id) REFERENCES user_need (id)');
        $this->addSql('ALTER TABLE user_subscription ADD CONSTRAINT FK_230A18D1A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_subscription ADD CONSTRAINT FK_230A18D19A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action_block DROP FOREIGN KEY FK_64D55B9E9D32F035');
        $this->addSql('ALTER TABLE action_block DROP FOREIGN KEY FK_64D55B9EE9ED820C');
        $this->addSql('ALTER TABLE feeling_block DROP FOREIGN KEY FK_389AAB9DCB9214C2');
        $this->addSql('ALTER TABLE feeling_block DROP FOREIGN KEY FK_389AAB9DE9ED820C');
        $this->addSql('ALTER TABLE feeling_need DROP FOREIGN KEY FK_6C8FD0F1CB9214C2');
        $this->addSql('ALTER TABLE feeling_need DROP FOREIGN KEY FK_6C8FD0F1624AF264');
        $this->addSql('ALTER TABLE need_action DROP FOREIGN KEY FK_3F2D1D23624AF264');
        $this->addSql('ALTER TABLE need_action DROP FOREIGN KEY FK_3F2D1D239D32F035');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CAA76ED395');
        $this->addSql('ALTER TABLE notification DROP FOREIGN KEY FK_BF5476CA9DD27DD1');
        $this->addSql('ALTER TABLE user_action DROP FOREIGN KEY FK_229E97AFA76ED395');
        $this->addSql('ALTER TABLE user_action DROP FOREIGN KEY FK_229E97AF9D32F035');
        $this->addSql('ALTER TABLE user_action DROP FOREIGN KEY FK_229E97AF3A47EC6E');
        $this->addSql('ALTER TABLE user_need DROP FOREIGN KEY FK_9C75208DA76ED395');
        $this->addSql('ALTER TABLE user_need DROP FOREIGN KEY FK_9C75208D624AF264');
        $this->addSql('ALTER TABLE user_need_history DROP FOREIGN KEY FK_1B686EC2A76ED395');
        $this->addSql('ALTER TABLE user_need_history DROP FOREIGN KEY FK_1B686EC23A47EC6E');
        $this->addSql('ALTER TABLE user_subscription DROP FOREIGN KEY FK_230A18D1A76ED395');
        $this->addSql('ALTER TABLE user_subscription DROP FOREIGN KEY FK_230A18D19A1887DC');
        $this->addSql('DROP TABLE action');
        $this->addSql('DROP TABLE action_block');
        $this->addSql('DROP TABLE block');
        $this->addSql('DROP TABLE feeling');
        $this->addSql('DROP TABLE feeling_block');
        $this->addSql('DROP TABLE feeling_need');
        $this->addSql('DROP TABLE need');
        $this->addSql('DROP TABLE need_action');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_action');
        $this->addSql('DROP TABLE user_need');
        $this->addSql('DROP TABLE user_need_history');
        $this->addSql('DROP TABLE user_subscription');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
