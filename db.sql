CREATE TABLE branches (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `address` VARCHAR(255),
    `phone_number` VARCHAR(20),
    `email_address` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE INDEX branches_name_idx1 ON `branches` (`name`);
CREATE INDEX branches_phone_number_idx1 ON `branches` (`phone_number`);
CREATE INDEX branches_email_address_idx1 ON `branches` (`email_address`);
CREATE INDEX branches_created_at_idx1 ON `branches` (`created_at`);
CREATE INDEX branches_updated_at_idx1 ON `branches` (`updated_at`);

CREATE TABLE users (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `type` ENUM('customer', 'driver', 'manager') NOT NULL,
    `username` VARCHAR(255) UNIQUE NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT  CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE INDEX users_username_idx1 ON `users` (`username`);
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
    `address` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
    `address` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
    `address` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX customers_first_name_idx1 ON `customers` (`first_name`);
CREATE INDEX customers_last_name_idx1 ON `customers` (`last_name`);
CREATE INDEX customers_full_name_idx1 ON `customers` (`full_name`);
CREATE INDEX customers_phone_number_idx1 ON `customers` (`phone_number`);
CREATE INDEX customers_created_at_idx1 ON `customers` (`created_at`);
CREATE INDEX customers_updated_at_idx1 ON `customers` (`updated_at`);

CREATE TABLE managers_notification_settings (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `manager_id` INT,
    `type` ENUM('in-app', 'email', 'sms') NOT NULL,
    `enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`manager_id`) REFERENCES `managers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX managers_notification_settings_type_idx1 ON `managers_notification_settings` (`type`);
CREATE INDEX managers_notification_settings_enabled_idx1 ON `managers_notification_settings` (`enabled`);
CREATE INDEX managers_notification_settings_created_at_idx1 ON `managers_notification_settings` (`created_at`);
CREATE INDEX managers_notification_settings_updated_at_idx1 ON `managers_notification_settings` (`updated_at`);

CREATE TABLE drivers_notification_settings (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `driver_id` INT,
    `type` ENUM('in-app', 'email', 'sms') NOT NULL,
    `enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX drivers_notification_settings_type_idx1 ON `drivers_notification_settings` (`type`);
CREATE INDEX drivers_notification_settings_enabled_idx1 ON `drivers_notification_settings` (`enabled`);
CREATE INDEX drivers_notification_settings_created_at_idx1 ON `drivers_notification_settings` (`created_at`);
CREATE INDEX drivers_notification_settings_updated_at_idx1 ON `drivers_notification_settings` (`updated_at`);

CREATE TABLE customers_notification_settings (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT,
    `type` ENUM('in-app', 'email', 'sms') NOT NULL,
    `enabled` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX customers_notification_settings_type_idx1 ON `customers_notification_settings` (`type`);
CREATE INDEX customers_notification_settings_enabled_idx1 ON `customers_notification_settings` (`enabled`);
CREATE INDEX customers_notification_settings_created_at_idx1 ON `customers_notification_settings` (`created_at`);
CREATE INDEX customers_notification_settings_updated_at_idx1 ON `customers_notification_settings` (`updated_at`);

CREATE TABLE customers_preferences (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT,
    `setting_name` VARCHAR(255),
    `setting_value` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX customers_preferences_setting_name_idx1 ON `customers_preferences` (`setting_name`);
CREATE INDEX customers_preferences_setting_value_idx1 ON `customers_preferences` (`setting_value`);
CREATE INDEX customers_preferences_created_at_idx1 ON `customers_preferences` (`created_at`);
CREATE INDEX customers_preferences_updated_at_idx1 ON `customers_preferences` (`updated_at`);

CREATE TABLE drivers_preferences (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `driver_id` INT,
    `setting_name` VARCHAR(255),
    `setting_value` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX drivers_preferences_setting_name_idx1 ON `drivers_preferences` (`setting_name`);
CREATE INDEX drivers_preferences_setting_value_idx1 ON `drivers_preferences` (`setting_value`);
CREATE INDEX drivers_preferences_created_at_idx1 ON `drivers_preferences` (`created_at`);
CREATE INDEX drivers_preferences_updated_at_idx1 ON `drivers_preferences` (`updated_at`);

CREATE TABLE promotional_codes (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT,
    `code` VARCHAR(255) NOT NULL,
    `description` VARCHAR(255),
    `discount` DECIMAL(10,2) NOT NULL,
    `expiration_date` DATE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`promotional_code_id`) REFERENCES `promotional_codes`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX pickup_requests_service_type_idx1 ON `pickup_requests` (`service_type`);
CREATE INDEX pickup_requests_created_at_idx1 ON `pickup_requests` (`created_at`);
CREATE INDEX pickup_requests_updated_at_idx1 ON `pickup_requests` (`updated_at`);

CREATE TABLE pickup_request_locations (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT,
    `location` VARCHAR(255) NOT NULL,
    `latitude` DECIMAL(9,6) NOT NULL,
    `longitude` DECIMAL(9,6) NOT NULL,
    FOREIGN KEY (`request_id`) REFERENCES `pickup_requests`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX pickup_request_locations_location_idx1 ON `pickup_request_locations` (`location`);
CREATE INDEX pickup_request_locations_latitude_idx1 ON `pickup_request_locations` (`latitude`);
CREATE INDEX pickup_request_locations_longitude_idx1 ON `pickup_request_locations` (`longitude`);

CREATE TABLE pickup_request_details (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT,
    `date` DATE NOT NULL,
    `time` TIME NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `note` TEXT,
    FOREIGN KEY (`request_id`) REFERENCES `pickup_requests`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX pickup_request_details_date_idx1 ON `pickup_request_details` (`date`);
CREATE INDEX pickup_request_details_time_idx1 ON `pickup_request_details` (`time`);
CREATE INDEX pickup_request_details_amount_idx1 ON `pickup_request_details` (`amount`);

CREATE TABLE pickup_request_payments (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT,
    `amount` DECIMAL(10,2) NOT NULL,
    `method` ENUM('Cash', 'MoMo', 'Card') NOT NULL,
    `status` ENUM('Failed', 'Successful') NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`request_id`) REFERENCES `pickup_requests`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX pickup_request_payments_amount_idx1 ON `pickup_request_payments` (`amount`);
CREATE INDEX pickup_request_payments_method_idx1 ON `pickup_request_payments` (`method`);
CREATE INDEX pickup_request_payments_status_idx1 ON `pickup_request_payments` (`status`);
CREATE INDEX pickup_request_payments_created_at_idx1 ON `pickup_request_payments` (`created_at`);
CREATE INDEX pickup_request_payments_updated_at_idx1 ON `pickup_request_payments` (`updated_at`);

CREATE TABLE pickup_requests_assignments (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `driver_id` INT,
    `request_id` INT,
    `status` ENUM('pending', 'accepted', 'in-progress', 'completed', 'cancelled') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`request_id`) REFERENCES `pickup_requests`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX pickup_requests_assignments_status_idx1 ON `pickup_requests_assignments` (`status`);
CREATE INDEX pickup_requests_assignments_created_at_idx1 ON `pickup_requests_assignments` (`created_at`);
CREATE INDEX pickup_requests_assignments_updated_at_idx1 ON `pickup_requests_assignments` (`updated_at`);

CREATE TABLE pickup_requests_driver_routes (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT,
    `driver_id` INT,
    `latitude` DECIMAL(9, 6) NOT NULL,
    `longitude` DECIMAL(9, 6) NOT NULL,
    `status` ENUM('in-progress', 'completed') DEFAULT 'in-progress',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`request_id`) REFERENCES `pickup_requests`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX pickup_requests_driver_routes_latitude_idx1 ON `pickup_requests_driver_routes` (`latitude`);
CREATE INDEX pickup_requests_driver_routes_longitude_idx1 ON `pickup_requests_driver_routes` (`longitude`);
CREATE INDEX pickup_requests_driver_routes_status_idx1 ON `pickup_requests_driver_routes` (`status`);
CREATE INDEX pickup_requests_driver_routes_created_at_idx1 ON `pickup_requests_driver_routes` (`created_at`);
CREATE INDEX pickup_requests_driver_routes_updated_at_idx1 ON `pickup_requests_driver_routes` (`updated_at`);

CREATE TABLE items (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `branch_id` INT,
    `name` VARCHAR(255) NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
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
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `pickup_requests`(id),
    FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
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
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX invoices_amount_idx1 ON `invoices` (`amount`);
CREATE INDEX invoices_discount_idx1 ON `invoices` (`discount`);
CREATE INDEX invoices_discount_amount_idx1 ON `invoices` (`discount_amount`);
CREATE INDEX invoices_actual_amount_idx1 ON `invoices` (`actual_amount`);
CREATE INDEX invoices_status_idx1 ON `invoices` (`status`);
CREATE INDEX invoices_created_at_idx1 ON `invoices` (`created_at`);
CREATE INDEX invoices_updated_at_idx1 ON `invoices` (`updated_at`);

CREATE TABLE invoice_payments (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `invoice_id` INT NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `method` ENUM('Cash', 'MoMo', 'Card') NOT NULL,
    `status` ENUM('Failed', 'Successful') NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`invoice_id`) REFERENCES `invoices`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX invoice_payments_amount_idx1 ON `invoice_payments` (`amount`);
CREATE INDEX invoice_payments_method_idx1 ON `invoice_payments` (`method`);
CREATE INDEX invoice_payments_status_idx1 ON `invoice_payments` (`status`);
CREATE INDEX invoice_payments_created_at_idx1 ON `invoice_payments` (`created_at`);
CREATE INDEX invoice_payments_updated_at_idx1 ON `invoice_payments` (`updated_at`);

CREATE TABLE delivery_requests (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT,
    `status` ENUM('pending', 'accepted', 'in-progress', 'completed', 'cancelled') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`branch_id`) REFERENCES `branches`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX delivery_requests_status_idx1 ON `delivery_requests` (`status`);
CREATE INDEX delivery_requests_created_at_idx1 ON `delivery_requests` (`created_at`);
CREATE INDEX delivery_requests_updated_at_idx1 ON `delivery_requests` (`updated_at`);

CREATE TABLE delivery_request_locations (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT,
    `location` VARCHAR(255) NOT NULL,
    `latitude` DECIMAL(9,6) NOT NULL,
    `longitude` DECIMAL(9,6) NOT NULL,
    FOREIGN KEY (`request_id`) REFERENCES `delivery_requests`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX delivery_request_locations_location_idx1 ON `delivery_request_locations` (`location`);
CREATE INDEX delivery_request_locations_latitude_idx1 ON `delivery_request_locations` (`latitude`);
CREATE INDEX delivery_request_locations_longitude_idx1 ON `delivery_request_locations` (`longitude`);

CREATE TABLE delivery_request_details (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT,
    `date` DATE NOT NULL,
    `time` TIME NOT NULL,
    `amount` DECIMAL(10,2) NOT NULL,
    `note` TEXT,
    FOREIGN KEY (`request_id`) REFERENCES `delivery_requests`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX delivery_request_details_date_idx1 ON `delivery_request_details` (`date`);
CREATE INDEX delivery_request_details_time_idx1 ON `delivery_request_details` (`time`);
CREATE INDEX delivery_request_details_amount_idx1 ON `delivery_request_details` (`amount`);

CREATE TABLE delivery_request_payments (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT,
    `amount` DECIMAL(10,2) NOT NULL,
    `method` ENUM('Cash', 'MoMo', 'Card') NOT NULL,
    `status` ENUM('Failed', 'Successful') NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`request_id`) REFERENCES `delivery_requests`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX delivery_request_payments_amount_idx1 ON `delivery_request_payments` (`amount`);
CREATE INDEX delivery_request_payments_method_idx1 ON `delivery_request_payments` (`method`);
CREATE INDEX delivery_request_payments_status_idx1 ON `delivery_request_payments` (`status`);
CREATE INDEX delivery_request_payments_created_at_idx1 ON `delivery_request_payments` (`created_at`);
CREATE INDEX delivery_request_payments_updated_at_idx1 ON `delivery_request_payments` (`created_at`);

CREATE TABLE delivery_requests_assignments (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT,
    `driver_id` INT,
    `status` ENUM('pending', 'accepted', 'in-progress', 'completed', 'cancelled') DEFAULT 'pending',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`request_id`) REFERENCES `delivery_requests`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX delivery_requests_assignments_status_idx1 ON `delivery_requests_assignments` (`status`);
CREATE INDEX delivery_requests_assignments_created_at_idx1 ON `delivery_requests_assignments` (`created_at`);
CREATE INDEX delivery_requests_assignments_updated_at_idx1 ON `delivery_requests_assignments` (`updated_at`);

CREATE TABLE delivery_requests_driver_routes (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT,
    `driver_id` INT,
    `latitude` DECIMAL(9, 6) NOT NULL,
    `longitude` DECIMAL(9, 6) NOT NULL,
    `status` ENUM('in-progress', 'completed') DEFAULT 'in-progress',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`request_id`) REFERENCES `delivery_requests`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX delivery_requests_driver_routes_latitude_idx1 ON `delivery_requests_driver_routes` (`latitude`);
CREATE INDEX delivery_requests_driver_routes_longitude_idx1 ON `delivery_requests_driver_routes` (`longitude`);
CREATE INDEX delivery_requests_driver_routes_status_idx1 ON `delivery_requests_driver_routes` (`status`);
CREATE INDEX delivery_requests_driver_routes_created_at_idx1 ON `delivery_requests_driver_routes` (`created_at`);
CREATE INDEX delivery_requests_driver_routes_updated_at_idx1 ON `delivery_requests_driver_routes` (`updated_at`);

CREATE TABLE pickup_requests_conversations (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT,
    `driver_id` INT,
    `customer_id` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`request_id`) REFERENCES `pickup_requests`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX pickup_requests_conversations_created_at_idx1 ON `pickup_requests_conversations` (`created_at`);
CREATE INDEX pickup_requests_conversations_updated_at_idx1 ON `pickup_requests_conversations` (`updated_at`);

CREATE TABLE pickup_requests_messages (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` INT,
    `sender` ENUM('customer', 'driver') NOT NULL,
    `message` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`conversation_id`) REFERENCES `pickup_requests_conversations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX pickup_requests_messages_sender_idx1 ON `pickup_requests_messages` (`sender`);
CREATE INDEX pickup_requests_messages_created_at_idx1 ON `pickup_requests_messages` (`created_at`);
CREATE INDEX pickup_requests_messages_updated_at_idx1 ON `pickup_requests_messages` (`updated_at`);

CREATE TABLE delivery_requests_conversations (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT,
    `driver_id` INT,
    `customer_id` INT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`request_id`) REFERENCES `pickup_requests`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX delivery_requests_conversations_created_at_idx1 ON `delivery_requests_conversations` (`created_at`);
CREATE INDEX delivery_requests_conversations_updated_at_idx1 ON `delivery_requests_conversations` (`updated_at`);

CREATE TABLE delivery_requests_messages (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `conversation_id` INT,
    `sender` ENUM('customer', 'driver') NOT NULL,
    `message` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`conversation_id`) REFERENCES `delivery_requests_conversations`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX delivery_requests_messages_sender_idx1 ON `delivery_requests_messages` (`sender`);
CREATE INDEX delivery_requests_messages_created_at_idx1 ON `delivery_requests_messages` (`created_at`);
CREATE INDEX delivery_requests_messages_updated_at_idx1 ON `delivery_requests_messages` (`updated_at`);

CREATE TABLE pickup_driver_ratings (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT,
    `customer_id` INT,
    `driver_id` INT,
    `rating` INT NOT NULL,
    `comment` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`request_id`) REFERENCES `delivery_requests`(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX pickup_driver_ratings_rating_idx1 ON `pickup_driver_ratings` (`rating`);
CREATE INDEX pickup_driver_ratings_created_at_idx1 ON `pickup_driver_ratings` (`created_at`);
CREATE INDEX pickup_driver_ratings_updated_at_idx1 ON `pickup_driver_ratings` (`updated_at`);

CREATE TABLE delivery_driver_ratings (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `request_id` INT,
    `customer_id` INT,
    `driver_id` INT,
    `rating` INT NOT NULL,
    `comment` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`request_id`) REFERENCES `delivery_requests`(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX delivery_driver_ratings_rating_idx1 ON `delivery_driver_ratings` (`rating`);
CREATE INDEX delivery_driver_ratings_created_at_idx1 ON `delivery_driver_ratings` (`created_at`);
CREATE INDEX delivery_driver_ratings_updated_at_idx1 ON `delivery_driver_ratings` (`updated_at`);

CREATE TABLE service_ratings (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT,
    `customer_id` INT,
    `rating` INT NOT NULL,
    `comment` TEXT,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX service_ratings_rating_idx1 ON `service_ratings` (`rating`);
CREATE INDEX service_ratings_created_at_idx1 ON `service_ratings` (`created_at`);
CREATE INDEX service_ratings_updated_at_idx1 ON `service_ratings` (`updated_at`);

CREATE TABLE managers_notifications (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `manager_id` INT,
    `type` ENUM('pickup', 'delivery', 'payment', 'general') NOT NULL,
    `title` VARCHAR(255),
    `message` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`manager_id`) REFERENCES `managers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX managers_notifications_type_idx1 ON `managers_notifications` (`type`);
CREATE INDEX managers_notifications_title_idx1 ON `managers_notifications` (`title`);
CREATE INDEX managers_notifications_is_read_idx1 ON `managers_notifications` (`is_read`);
CREATE INDEX managers_notifications_created_at_idx1 ON `managers_notifications` (`created_at`);
CREATE INDEX managers_notifications_updated_at_idx1 ON `managers_notifications` (`updated_at`);

CREATE TABLE drivers_notifications (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `driver_id` INT,
    `type` ENUM('pickup', 'delivery', 'payment', 'general') NOT NULL,
    `title` VARCHAR(255),
    `message` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`driver_id`) REFERENCES `drivers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX drivers_notifications_type_idx1 ON `drivers_notifications` (`type`);
CREATE INDEX drivers_notifications_title_idx1 ON `drivers_notifications` (`title`);
CREATE INDEX drivers_notifications_is_read_idx1 ON `drivers_notifications` (`is_read`);
CREATE INDEX drivers_notifications_created_at_idx1 ON `drivers_notifications` (`created_at`);
CREATE INDEX drivers_notifications_updated_at_idx1 ON `drivers_notifications` (`updated_at`);

CREATE TABLE customers_notifications (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `customer_id` INT,
    `type` ENUM('pickup', 'delivery', 'payment', 'general') NOT NULL,
    `title` VARCHAR(255),
    `message` TEXT,
    `is_read` BOOLEAN DEFAULT FALSE,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
);
CREATE INDEX customers_notifications_type_idx1 ON `customers_notifications` (`type`);
CREATE INDEX customers_notifications_title_idx1 ON `customers_notifications` (`title`);
CREATE INDEX customers_notifications_is_read_idx1 ON `customers_notifications` (`is_read`);
CREATE INDEX customers_notifications_created_at_idx1 ON `customers_notifications` (`created_at`);
CREATE INDEX customers_notifications_updated_at_idx1 ON `customers_notifications` (`updated_at`);