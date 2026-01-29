CREATE TABLE `Plan` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `duration` INT NOT NULL,
    `is_active` BOOLEAN DEFAULT TRUE,
    `slug` VARCHAR(255) NULL,
    `stripe_price_id` VARCHAR(255) NULL,
    `stripe_product_id` VARCHAR(255) NULL,
    `features` JSON NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `Need` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `type` VARCHAR(50) NULL,
    `fulfilment` VARCHAR(255) NULL,
    `icon` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `Action` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `intension` VARCHAR(255) NULL,
    `is_doable_now` BOOLEAN DEFAULT FALSE,
    `duration` INT NULL,
    `type` VARCHAR(50) NULL,
    `icon` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `Block` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(255) NOT NULL,
    `description` TEXT NULL,
    `type` VARCHAR(50) NULL,
    `beliefs` TEXT NULL,
    `reframings` TEXT NULL,
    `icon` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `User` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(255) NOT NULL UNIQUE,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `roles` JSON NULL,
    `dashboard_illustration` VARCHAR(255) NULL,
    `is_verified` BOOLEAN DEFAULT FALSE,
    `stripe_customer_id` VARCHAR(255) NULL,
    `subscription_id` INT UNSIGNED NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE `Subscription` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `plan_id` INT UNSIGNED NOT NULL,
    `transaction_id` VARCHAR(255) NULL,
    `start_date` DATETIME NOT NULL,
    `end_date` DATETIME NULL,
    `renewal_date` DATETIME NULL,
    `is_recurring` BOOLEAN DEFAULT FALSE,
    `is_active` BOOLEAN DEFAULT TRUE,
    `status` ENUM('active','canceled','expired') NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_subscription_user` FOREIGN KEY (`user_id`) REFERENCES `User`(`id`),
    CONSTRAINT `fk_subscription_plan` FOREIGN KEY (`plan_id`) REFERENCES `Plan`(`id`)
);

ALTER TABLE `User` ADD CONSTRAINT `fk_user_subscription` FOREIGN KEY (`subscription_id`) REFERENCES `Subscription`(`id`);

CREATE TABLE `Feeling` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `emotion` VARCHAR(255) NULL,
    `triggers` TEXT NULL,
    `color` VARCHAR(50) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_feeling_user` FOREIGN KEY (`user_id`) REFERENCES `User`(`id`)
);

CREATE TABLE `UserNeed` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `need_id` INT UNSIGNED NOT NULL,
    `priority` INT NULL,
    `score` INT NULL,
    `last_updated` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_userneed_user` FOREIGN KEY (`user_id`) REFERENCES `User`(`id`),
    CONSTRAINT `fk_userneed_need` FOREIGN KEY (`need_id`) REFERENCES `Need`(`id`)
);

CREATE TABLE `UserAction` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `user_need_id` INT UNSIGNED NULL,
    `action_id` INT UNSIGNED NOT NULL,
    `deadline` DATETIME NULL,
    `start_date` DATETIME NULL,
    `frequency` VARCHAR(50) NULL,
    `completions` INT NULL,
    `is_checked` BOOLEAN DEFAULT FALSE,
    `is_recurring` BOOLEAN DEFAULT FALSE,
    `status` ENUM('pending','completed') NULL,
    `last_update` DATETIME NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_useraction_user` FOREIGN KEY (`user_id`) REFERENCES `User`(`id`),
    CONSTRAINT `fk_useraction_userneed` FOREIGN KEY (`user_need_id`) REFERENCES `UserNeed`(`id`),
    CONSTRAINT `fk_useraction_action` FOREIGN KEY (`action_id`) REFERENCES `Action`(`id`)
);

CREATE TABLE `Notification` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL,
    `user_action_id` INT UNSIGNED NULL,
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `is_read` BOOLEAN DEFAULT FALSE,
    `received_at` DATETIME NULL,
    `type` VARCHAR(50) NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT `fk_notification_user` FOREIGN KEY (`user_id`) REFERENCES `User`(`id`),
    CONSTRAINT `fk_notification_useraction` FOREIGN KEY (`user_action_id`) REFERENCES `UserAction`(`id`)
);

CREATE TABLE `Feeling_Block` (
    `feeling_id` INT UNSIGNED NOT NULL,
    `block_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY(`feeling_id`,`block_id`),
    CONSTRAINT `fk_fb_feeling` FOREIGN KEY (`feeling_id`) REFERENCES `Feeling`(`id`),
    CONSTRAINT `fk_fb_block` FOREIGN KEY (`block_id`) REFERENCES `Block`(`id`)
);

CREATE TABLE `Feeling_Need` (
    `feeling_id` INT UNSIGNED NOT NULL,
    `need_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY(`feeling_id`,`need_id`),
    CONSTRAINT `fk_fn_feeling` FOREIGN KEY (`feeling_id`) REFERENCES `Feeling`(`id`),
    CONSTRAINT `fk_fn_need` FOREIGN KEY (`need_id`) REFERENCES `Need`(`id`)
);

CREATE TABLE `Need_Action` (
    `need_id` INT UNSIGNED NOT NULL,
    `action_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY(`need_id`,`action_id`),
    CONSTRAINT `fk_na_need` FOREIGN KEY (`need_id`) REFERENCES `Need`(`id`),
    CONSTRAINT `fk_na_action` FOREIGN KEY (`action_id`) REFERENCES `Action`(`id`)
);

CREATE TABLE `Block_Action` (
    `block_id` INT UNSIGNED NOT NULL,
    `action_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY(`block_id`,`action_id`),
    CONSTRAINT `fk_ba_block` FOREIGN KEY (`block_id`) REFERENCES `Block`(`id`),
    CONSTRAINT `fk_ba_action` FOREIGN KEY (`action_id`) REFERENCES `Action`(`id`)
);
