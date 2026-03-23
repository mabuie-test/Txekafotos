CREATE DATABASE IF NOT EXISTS txekafotos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE txekafotos;

CREATE TABLE admins (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    email VARCHAR(190) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'super_admin',
    last_login_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tracking_code VARCHAR(40) NOT NULL UNIQUE,
    tracking_token CHAR(64) NOT NULL UNIQUE,
    client_name VARCHAR(150) NOT NULL,
    client_phone VARCHAR(20) NOT NULL,
    service_type VARCHAR(80) NULL,
    description TEXT NOT NULL,
    primary_image_path VARCHAR(255) NOT NULL,
    edited_image_path VARCHAR(255) NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 45.00,
    status ENUM('pendente_pagamento','pagamento_em_analise','pago','em_edicao','revisao','concluido','aprovado','cancelado','falhou_pagamento') NOT NULL DEFAULT 'pendente_pagamento',
    terms_accepted TINYINT(1) NOT NULL DEFAULT 0,
    revisions_used TINYINT UNSIGNED NOT NULL DEFAULT 0,
    internal_notes TEXT NULL,
    payment_confirmed_at DATETIME NULL,
    approved_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_orders_status_created (status, created_at),
    INDEX idx_orders_phone (client_phone),
    INDEX idx_orders_service_type (service_type)
);

CREATE TABLE order_images (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    image_type ENUM('primary','extra','edited') NOT NULL DEFAULT 'extra',
    file_path VARCHAR(255) NOT NULL,
    original_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    file_size INT UNSIGNED NOT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_order_images_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_order_images_order (order_id, image_type)
);

CREATE TABLE transactions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    debito_reference VARCHAR(120) NOT NULL UNIQUE,
    payment_method VARCHAR(50) NOT NULL DEFAULT 'mpesa',
    amount DECIMAL(10,2) NOT NULL,
    status ENUM('pending','processing','completed','failed','cancelled') NOT NULL DEFAULT 'pending',
    raw_response JSON NULL,
    last_checked_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_transactions_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_transactions_status (status),
    INDEX idx_transactions_order (order_id)
);

CREATE TABLE financial_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type ENUM('entrada','estorno','ajuste','saida') NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    description VARCHAR(255) NOT NULL,
    related_order_id INT UNSIGNED NULL,
    related_transaction_id INT UNSIGNED NULL,
    created_by_admin_id INT UNSIGNED NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_financial_logs_order FOREIGN KEY (related_order_id) REFERENCES orders(id) ON DELETE SET NULL,
    CONSTRAINT fk_financial_logs_transaction FOREIGN KEY (related_transaction_id) REFERENCES transactions(id) ON DELETE SET NULL,
    CONSTRAINT fk_financial_logs_admin FOREIGN KEY (created_by_admin_id) REFERENCES admins(id) ON DELETE SET NULL,
    INDEX idx_financial_logs_type_date (type, created_at)
);

CREATE TABLE revisions (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    client_message TEXT NOT NULL,
    admin_response TEXT NULL,
    status ENUM('pending','answered','resolved') NOT NULL DEFAULT 'pending',
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_revisions_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_revisions_order_status (order_id, status)
);

CREATE TABLE feedbacks (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL UNIQUE,
    client_name VARCHAR(150) NOT NULL,
    rating TINYINT UNSIGNED NOT NULL,
    message TEXT NOT NULL,
    is_published TINYINT(1) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_feedbacks_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT chk_feedback_rating CHECK (rating BETWEEN 1 AND 5),
    INDEX idx_feedbacks_published_rating (is_published, rating)
);

CREATE TABLE showcases (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id INT UNSIGNED NOT NULL,
    before_image VARCHAR(255) NOT NULL,
    after_image VARCHAR(255) NOT NULL,
    title VARCHAR(150) NOT NULL,
    description TEXT NULL,
    sort_order SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    published_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_showcases_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    INDEX idx_showcases_active_featured (is_active, is_featured, sort_order)
);

CREATE TABLE homepage_content (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hero_title VARCHAR(190) NOT NULL,
    hero_subtitle TEXT NOT NULL,
    hero_cta_text VARCHAR(120) NOT NULL,
    hero_secondary_cta_text VARCHAR(120) NOT NULL,
    hero_badge_text VARCHAR(120) NULL,
    section_benefits_title VARCHAR(190) NOT NULL,
    section_feedback_title VARCHAR(190) NOT NULL,
    section_showcase_title VARCHAR(190) NOT NULL,
    final_cta_title VARCHAR(190) NOT NULL,
    final_cta_text TEXT NOT NULL,
    benefits_text TEXT NULL,
    stats_json JSON NULL,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE marketing_banners (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    subtitle VARCHAR(255) NULL,
    button_text VARCHAR(120) NULL,
    button_link VARCHAR(255) NULL,
    image_path VARCHAR(255) NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_marketing_banners_active (is_active)
);

CREATE TABLE activity_logs (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    actor_type ENUM('admin','system','client') NOT NULL,
    actor_id INT UNSIGNED NULL,
    action VARCHAR(120) NOT NULL,
    entity_type VARCHAR(120) NOT NULL,
    entity_id INT UNSIGNED NOT NULL,
    description VARCHAR(255) NOT NULL,
    metadata JSON NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_activity_logs_entity (entity_type, entity_id),
    INDEX idx_activity_logs_action_date (action, created_at)
);
