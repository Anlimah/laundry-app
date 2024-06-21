CREATE TABLE addresses (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `street` VARCHAR(255),
    `city` VARCHAR(255),
    `state` VARCHAR(255),
    `zip_code` VARCHAR(20),
    `country` VARCHAR(255),
    `latitude` DECIMAL(9,6),
    `longitude` DECIMAL(9,6),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE INDEX addresses_city_idx1 ON `addresses` (`city`);
CREATE INDEX addresses_state_idx1 ON `addresses` (`state`);
CREATE INDEX addresses_zip_code_idx1 ON `addresses` (`zip_code`);
CREATE INDEX addresses_country_idx1 ON `addresses` (`country`);
CREATE INDEX addresses_created_at_idx1 ON `addresses` (`created_at`);
CREATE INDEX addresses_updated_at_idx1 ON `addresses` (`updated_at`);

CREATE TABLE branches (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `address_id` INT,
    `phone_number` VARCHAR(20),
    `email` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`address_id`) REFERENCES `addresses`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
);
CREATE INDEX branches_name_idx1 ON `branches` (`name`);
CREATE INDEX branches_phone_number_idx1 ON `branches` (`phone_number`);
CREATE INDEX branches_email_idx1 ON `branches` (`email`);
CREATE INDEX branches_created_at_idx1 ON `branches` (`created_at`);
CREATE INDEX branches_updated_at_idx1 ON `branches` (`updated_at`);

CREATE TABLE roles (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL UNIQUE,
    `description` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE INDEX roles_name_idx1 ON `roles` (`name`);
CREATE INDEX roles_created_at_idx1 ON `roles` (`created_at`);
CREATE INDEX roles_updated_at_idx1 ON `roles` (`updated_at`);
INSERT INTO roles (`name`, `description`) VALUES 
('customer', 'customer of the branch'), ('driver', 'driver of the branch'), ('manager', 'manager of the branch');

CREATE TABLE users (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `role_id` INT,
    `email` VARCHAR(255) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT  CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`role_id`) REFERENCES `roles`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX users_email_idx1 ON `users` (`email`);
CREATE INDEX users_password_idx1 ON `users` (`password`);
CREATE INDEX users_created_at_idx1 ON `users` (`created_at`);
CREATE INDEX users_updated_at_idx1 ON `users` (`updated_at`);

CREATE TABLE managers (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `branch_id` INT,
    `first_name` VARCHAR(255) NOT NULL,
    `last_name` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(255) GENERATED ALWAYS AS (CONCAT(`first_name`,' ',`last_name`)) STORED,
    `phone_number` VARCHAR(20) NOT NULL,
    `address_id` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`address_id`) REFERENCES `addresses`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
);
CREATE INDEX managers_first_name_idx1 ON `managers` (`first_name`);
CREATE INDEX managers_last_name_idx1 ON `managers` (`last_name`);
CREATE INDEX managers_full_name_idx1 ON `managers` (`full_name`);
CREATE INDEX managers_phone_number_idx1 ON `managers` (`phone_number`);
CREATE INDEX managers_created_at_idx1 ON `managers` (`created_at`);
CREATE INDEX managers_updated_at_idx1 ON `managers` (`updated_at`);

CREATE TABLE vehicles (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT NOT NULL,
    `number` VARCHAR(255) UNIQUE NOT NULL,
    `type` ENUM('car', 'motorcycle') DEFAULT 'car',
    `model` VARCHAR(255),
    `year` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX vehicles_number_idx1 ON `vehicles` (`number`);
CREATE INDEX vehicles_type_idx1 ON `vehicles` (`type`);
CREATE INDEX vehicles_created_at_idx1 ON `vehicles` (`created_at`);
CREATE INDEX vehicles_updated_at_idx1 ON `vehicles` (`updated_at`);

CREATE TABLE drivers (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `branch_id` INT,
    `vehicle_id` INT,
    `first_name` VARCHAR(255) NOT NULL,
    `last_name` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(255) GENERATED ALWAYS AS (CONCAT(`first_name`,' ',`last_name`)) STORED,
    `phone_number` VARCHAR(20) NOT NULL,
    `address_id` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`address_id`) REFERENCES `addresses`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
);
CREATE INDEX drivers_first_name_idx1 ON `drivers` (`first_name`);
CREATE INDEX drivers_last_name_idx1 ON `drivers` (`last_name`);
CREATE INDEX drivers_full_name_idx1 ON `drivers` (`full_name`);
CREATE INDEX drivers_phone_number_idx1 ON `drivers` (`phone_number`);
CREATE INDEX drivers_created_at_idx1 ON `drivers` (`created_at`);
CREATE INDEX drivers_updated_at_idx1 ON `drivers` (`updated_at`);

CREATE TABLE customers (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `first_name` VARCHAR(255) NOT NULL,
    `last_name` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(255) GENERATED ALWAYS AS (CONCAT(`first_name`,' ',`last_name`)) STORED,
    `phone_number` VARCHAR(20) NOT NULL,
    `address_id` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`address_id`) REFERENCES `addresses`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
);
CREATE INDEX customers_first_name_idx1 ON `customers` (`first_name`);
CREATE INDEX customers_last_name_idx1 ON `customers` (`last_name`);
CREATE INDEX customers_full_name_idx1 ON `customers` (`full_name`);
CREATE INDEX customers_phone_number_idx1 ON `customers` (`phone_number`);
CREATE INDEX customers_created_at_idx1 ON `customers` (`created_at`);
CREATE INDEX customers_updated_at_idx1 ON `customers` (`updated_at`);

CREATE TABLE notification_settings (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `type` ENUM('in-app', 'email', 'sms') NOT NULL,
    `enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX notification_settings_type_idx1 ON `notification_settings` (`type`);
CREATE INDEX notification_settings_enabled_idx1 ON `notification_settings` (`enabled`);
CREATE INDEX notification_settings_created_at_idx1 ON `notification_settings` (`created_at`);
CREATE INDEX notification_settings_updated_at_idx1 ON `notification_settings` (`updated_at`);

CREATE TABLE user_preferences (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT,
    `setting_name` VARCHAR(255),
    `setting_value` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX user_preferences_setting_name_idx1 ON `user_preferences` (`setting_name`);
CREATE INDEX user_preferences_setting_value_idx1 ON `user_preferences` (`setting_value`);
CREATE INDEX user_preferences_created_at_idx1 ON `user_preferences` (`created_at`);
CREATE INDEX user_preferences_updated_at_idx1 ON `user_preferences` (`updated_at`);

CREATE TABLE promotional_codes (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT,
    `code` VARCHAR(255) NOT NULL,
    `description` VARCHAR(255),
    `discount` DECIMAL(10,2) NOT NULL,
    `expiration_date` DATE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE INDEX promotional_codes_code_idx1 ON `promotional_codes` (`code`);
CREATE INDEX promotional_codes_discount_idx1 ON `promotional_codes` (`discount`);
CREATE INDEX promotional_codes_expiration_date_idx1 ON `promotional_codes` (`expiration_date`);
CREATE INDEX promotional_codes_created_at_idx1 ON `promotional_codes` (`created_at`);
CREATE INDEX promotional_codes_updated_at_idx1 ON `promotional_codes` (`updated_at`);

CREATE TABLE customers_promotional_codes (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `promotional_code_id` INT,
    `customer_id` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`promotional_code_id`) REFERENCES `promotional_codes`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX customers_promotional_codes_created_at_idx1 ON `customers_promotional_codes` (`created_at`);
CREATE INDEX customers_promotional_codes_updated_at_idx1 ON `customers_promotional_codes` (`updated_at`);

CREATE TABLE pickup_requests (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(255) NOT NULL UNIQUE,
    `customer_id` INT,
    `branch_id` INT,
    `service_type` ENUM('wash', 'wash and iron') NOT NULL,
    `status` ENUM('pending', 'accepted', 'in-progress', 'completed', 'cancelled') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX pickup_requests_service_type_idx1 ON `pickup_requests` (`service_type`);
CREATE INDEX pickup_requests_created_at_idx1 ON `pickup_requests` (`created_at`);
CREATE INDEX pickup_requests_updated_at_idx1 ON `pickup_requests` (`updated_at`);

CREATE TABLE items (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT,
    `name` VARCHAR(255) NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX items_name_idx1 ON `items` (`name`);
CREATE INDEX items_amount_idx1 ON `items` (`amount`);
CREATE INDEX items_created_at_idx1 ON `items` (`created_at`);
CREATE INDEX items_updated_at_idx1 ON `items` (`updated_at`);

CREATE TABLE orders (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT,
    `customer_id` INT,
    `code` VARCHAR(255) NOT NULL UNIQUE,
    `customer_promotional_code_id` INT,
    `status` ENUM('ready for washing', 'ready for ironing', 'ready for pickup or delivery') DEFAULT 'ready for washing',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`customer_promotional_code_id`) REFERENCES `customers_promotional_codes`(`id`) ON DELETE SET NULL ON UPDATE CASCADE
);
CREATE INDEX orders_status_idx1 ON `orders` (`status`);
CREATE INDEX orders_created_at_idx1 ON `orders` (`created_at`);
CREATE INDEX orders_updated_at_idx1 ON `orders` (`updated_at`);

CREATE TABLE order_items (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT,
    `item_id` INT,
    `amount` DECIMAL(10,2) NOT NULL,
    `quantity` INT NOT NULL,
    `total_amount` DECIMAL(10, 2) GENERATED ALWAYS AS (`amount` * `quantity`) STORED,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX order_items_amount_idx1 ON `order_items` (`amount`);
CREATE INDEX order_items_quantity_idx1 ON `order_items` (`quantity`);
CREATE INDEX order_items_total_amount_idx1 ON `order_items` (`total_amount`);
CREATE INDEX order_items_created_at_idx1 ON `order_items` (`created_at`);
CREATE INDEX order_items_updated_at_idx1 ON `order_items` (`updated_at`);

CREATE TABLE invoices (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT,
    `amount` DECIMAL(10,2) NOT NULL,
    `discount` DECIMAL(10,2) DEFAULT 0,
    `discount_amount` DECIMAL(10,2) GENERATED ALWAYS AS (`amount` * `discount`) STORED,
    `actual_amount` DECIMAL(10,2) GENERATED ALWAYS AS (`amount` - `discount_amount`) STORED,
    `status` ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX invoices_amount_idx1 ON `invoices` (`amount`);
CREATE INDEX invoices_discount_idx1 ON `invoices` (`discount`);
CREATE INDEX invoices_discount_amount_idx1 ON `invoices` (`discount_amount`);
CREATE INDEX invoices_actual_amount_idx1 ON `invoices` (`actual_amount`);
CREATE INDEX invoices_status_idx1 ON `invoices` (`status`);
CREATE INDEX invoices_created_at_idx1 ON `invoices` (`created_at`);
CREATE INDEX invoices_updated_at_idx1 ON `invoices` (`updated_at`);

CREATE TABLE delivery_requests (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT,
    `status` ENUM('pending', 'accepted', 'in-progress', 'completed', 'cancelled') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX delivery_requests_status_idx1 ON `delivery_requests` (`status`);
CREATE INDEX delivery_requests_created_at_idx1 ON `delivery_requests` (`created_at`);
CREATE INDEX delivery_requests_updated_at_idx1 ON `delivery_requests` (`updated_at`);

CREATE TABLE payments (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_type` ENUM('pickup', 'invoice', 'delivery') NOT NULL,
    `request_id` INT NOT NULL,
    `transaction_id` VARCHAR(255),  -- Store external transaction IDs if applicable
    `amount` DECIMAL(10,2) NOT NULL,
    `method` ENUM('Cash', 'MoMo', 'Card') NOT NULL,
    `status` ENUM('pending', 'completed', 'failed') NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE INDEX payments_request_type_idx1 ON `payments` (`request_type`);
CREATE INDEX payments_request_id_idx1 ON `payments` (`request_id`);
CREATE INDEX payments_transaction_id_idx1 ON `payments` (`transaction_id`);
CREATE INDEX payments_amount_idx1 ON `payments` (`amount`);
CREATE INDEX payments_method_idx1 ON `payments` (`method`);
CREATE INDEX payments_status_idx1 ON `payments` (`status`);
CREATE INDEX payments_created_at_idx1 ON `payments` (`created_at`);
CREATE INDEX payments_updated_at_idx1 ON `payments` (`updated_at`);

CREATE TABLE locations (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_type` ENUM('pickup', 'delivery') NOT NULL,
    `request_id` INT NOT NULL,
    `location` VARCHAR(255) NOT NULL,
    `latitude` DECIMAL(9,6) NOT NULL,
    `longitude` DECIMAL(9,6) NOT NULL,
--    `coordinates` POINT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE INDEX locations_request_type_idx1 ON `locations` (`request_type`);
CREATE INDEX locations_request_id_idx1 ON `locations` (`request_id`);
CREATE INDEX locations_location_idx1 ON `locations` (`location`);
CREATE INDEX locations_latitude_idx1 ON `locations` (`latitude`);
CREATE INDEX locations_longitude_idx1 ON `locations` (`longitude`);
CREATE INDEX locations_created_at_idx1 ON `locations` (`created_at`);
CREATE INDEX locations_updated_at_idx1 ON `locations` (`updated_at`);
-- UPDATE `locations` SET `coordinates` = POINT(`longitude`, `latitude`); -- Populate coordinates column
-- CREATE SPATIAL INDEX locations_spatial_idx ON `locations` (`coordinates`);

CREATE TABLE request_details (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_type` ENUM('pickup', 'delivery') NOT NULL,
    `request_id` INT NOT NULL,
    `date` DATE NOT NULL,
    `time` TIME NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `note` TEXT
);
CREATE INDEX request_details_request_type_idx1 ON `request_details` (`request_type`);
CREATE INDEX request_details_request_id_idx1 ON `request_details` (`request_id`);
CREATE INDEX request_details_date_idx1 ON `request_details` (`date`);
CREATE INDEX request_details_time_idx1 ON `request_details` (`time`);
CREATE INDEX request_details_amount_idx1 ON `request_details` (`amount`);

CREATE TABLE request_assignments (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `driver_id` INT,
    `request_type` ENUM('pickup', 'delivery') NOT NULL,
    `request_id` INT NOT NULL,
    `status` ENUM('pending', 'accepted', 'in-progress', 'completed', 'cancelled') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX request_assignments_request_type_idx1 ON `request_assignments` (`request_type`);
CREATE INDEX request_assignments_request_id_idx1 ON `request_assignments` (`request_id`);
CREATE INDEX request_assignments_status_idx1 ON `request_assignments` (`status`);
CREATE INDEX request_assignments_created_at_idx1 ON `request_assignments` (`created_at`);
CREATE INDEX request_assignments_updated_at_idx1 ON `request_assignments` (`updated_at`);

CREATE TABLE request_driver_routes (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `driver_id` INT,
    `request_type` ENUM('pickup', 'delivery') NOT NULL,
    `request_id` INT NOT NULL,
    `latitude` DECIMAL(9, 6) NOT NULL,
    `longitude` DECIMAL(9, 6) NOT NULL,
    `status` ENUM('in-progress', 'completed') DEFAULT 'in-progress',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX request_driver_routes_request_type_idx1 ON `request_driver_routes` (`request_type`);
CREATE INDEX request_driver_routes_request_id_idx1 ON `request_driver_routes` (`request_id`);
CREATE INDEX request_driver_routes_latitude_idx1 ON `request_driver_routes` (`latitude`);
CREATE INDEX request_driver_routes_longitude_idx1 ON `request_driver_routes` (`longitude`);
CREATE INDEX request_driver_routes_status_idx1 ON `request_driver_routes` (`status`);
CREATE INDEX request_driver_routes_created_at_idx1 ON `request_driver_routes` (`created_at`);
CREATE INDEX request_driver_routes_updated_at_idx1 ON `request_driver_routes` (`updated_at`);

CREATE TABLE request_conversations (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `driver_id` INT,
    `customer_id` INT,
    `request_type` ENUM('pickup', 'delivery') NOT NULL,
    `request_id` INT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX request_conversations_request_type_idx1 ON `request_conversations` (`request_type`);
CREATE INDEX request_conversations_request_id_idx1 ON `request_conversations` (`request_id`);
CREATE INDEX request_conversations_created_at_idx1 ON `request_conversations` (`created_at`);
CREATE INDEX request_conversations_updated_at_idx1 ON `request_conversations` (`updated_at`);

CREATE TABLE conversation_messages (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` INT,
    `sender` ENUM('customer', 'driver') NOT NULL,
    `message` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`conversation_id`) REFERENCES `request_conversations`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX conversation_messages_sender_idx1 ON `conversation_messages` (`sender`);
CREATE INDEX conversation_messages_created_at_idx1 ON `conversation_messages` (`created_at`);
CREATE INDEX conversation_messages_updated_at_idx1 ON `conversation_messages` (`updated_at`);

CREATE TABLE driver_ratings (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT,
    `driver_id` INT,
    `request_type` ENUM('pickup', 'delivery') NOT NULL,
    `request_id` INT NOT NULL,
    `rating` INT NOT NULL,
    `comment` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX driver_ratings_request_type_idx1 ON `driver_ratings` (`request_type`);
CREATE INDEX driver_ratings_request_id_idx1 ON `driver_ratings` (`request_id`);
CREATE INDEX driver_ratings_rating_idx1 ON `driver_ratings` (`rating`);
CREATE INDEX driver_ratings_created_at_idx1 ON `driver_ratings` (`created_at`);
CREATE INDEX driver_ratings_updated_at_idx1 ON `driver_ratings` (`updated_at`);

CREATE TABLE service_ratings (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT,
    `customer_id` INT,
    `rating` INT NOT NULL,
    `comment` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX service_ratings_rating_idx1 ON `service_ratings` (`rating`);
CREATE INDEX service_ratings_created_at_idx1 ON `service_ratings` (`created_at`);
CREATE INDEX service_ratings_updated_at_idx1 ON `service_ratings` (`updated_at`);

CREATE TABLE notifications (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `type` ENUM('pickup', 'delivery', 'payment', 'general') NOT NULL,
    `title` VARCHAR(255),
    `message` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE NO ACTION ON UPDATE CASCADE
);
CREATE INDEX notifications_type_idx1 ON `notifications` (`type`);
CREATE INDEX notifications_title_idx1 ON `notifications` (`title`);
CREATE INDEX notifications_is_read_idx1 ON `notifications` (`is_read`);
CREATE INDEX notifications_created_at_idx1 ON `notifications` (`created_at`);
CREATE INDEX notifications_updated_at_idx1 ON `notifications` (`updated_at`);