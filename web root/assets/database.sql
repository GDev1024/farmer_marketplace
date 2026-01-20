-- Create Database
CREATE DATABASE IF NOT EXISTS grenada_farmers;
USE grenada_farmers;

-- Migrations Table (for tracking database updates)
CREATE TABLE migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration_name VARCHAR(255) NOT NULL UNIQUE,
    executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Users Table
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    farmer_id VARCHAR(100),
    farmer_verified BOOLEAN DEFAULT 0,
    bio TEXT,
    profile_image VARCHAR(255),
    phone VARCHAR(20),
    location VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email)
);

-- Listings Table (Products)
CREATE TABLE listings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    category ENUM('vegetables', 'fruits', 'herbs', 'grains', 'other') NOT NULL,
    description TEXT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    unit VARCHAR(50) NOT NULL,
    quantity INT NOT NULL,
    image_path VARCHAR(255) NULL,
    thumbnail_path VARCHAR(255) NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_category (category),
    INDEX idx_active (is_active),
    INDEX idx_listings_has_image (image_path)
);

-- Orders Table
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'shipped', 'delivered', 'cancelled') DEFAULT 'pending',
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_method VARCHAR(50) NULL,
    payment_transaction_id VARCHAR(255) NULL,
    shipping_address VARCHAR(255),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status),
    INDEX idx_created_at (created_at)
);

-- Order Items Table
CREATE TABLE order_items (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    listing_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (listing_id) REFERENCES listings(id) ON DELETE RESTRICT,
    INDEX idx_order_id (order_id),
    INDEX idx_listing_id (listing_id)
);

-- Messages Table
CREATE TABLE messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_sender_id (sender_id),
    INDEX idx_receiver_id (receiver_id),
    INDEX idx_created_at (created_at)
);

-- Reviews Table
CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    reviewer_id INT NOT NULL,
    seller_id INT NOT NULL,
    rating INT CHECK (rating >= 1 AND rating <= 5),
    review_text TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (seller_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_seller_id (seller_id),
    INDEX idx_reviewer_id (reviewer_id)
);

-- Notifications Table
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    type VARCHAR(50),
    title VARCHAR(255),
    message TEXT,
    is_read BOOLEAN DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read)
);

-- Cart Table (optional, can use sessions instead)
CREATE TABLE carts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE,
    items JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Sample Data for Testing
INSERT INTO users (name, email, password, farmer_verified) VALUES
('John Smith', 'john@farmers.com', '$2y$10$YIjlrJ5Z0Z5Z0Z5Z0Z5Z0Z5Z0Z5Z0Z5Z0Z5Z0Z', 1),
('Mary Johnson', 'mary@farmers.com', '$2y$10$YIjlrJ5Z0Z5Z0Z5Z0Z5Z0Z5Z0Z5Z0Z5Z0Z5Z0Z', 1),
('Robert Brown', 'robert@farmers.com', '$2y$10$YIjlrJ5Z0Z5Z0Z5Z0Z5Z0Z5Z0Z5Z0Z5Z0Z5Z0Z', 0);

INSERT INTO listings (user_id, product_name, category, description, price, unit, quantity) VALUES
(1, 'Fresh Tomatoes', 'vegetables', 'Ripe, juicy tomatoes picked fresh this morning', 5.50, 'lbs', 50),
(1, 'Organic Carrots', 'vegetables', 'Crunchy, sweet organic carrots grown locally', 4.00, 'lbs', 75),
(2, 'Mango', 'fruits', 'Sweet and delicious mangoes in season', 8.50, 'pieces', 30),
(2, 'Passion Fruit', 'fruits', 'Fresh passion fruit for juices and desserts', 12.00, 'pieces', 25),
(3, 'Basil', 'herbs', 'Fresh aromatic basil for cooking', 3.50, 'bunches', 20),
(3, 'Cilantro', 'herbs', 'Fresh cilantro for your favorite dishes', 2.50, 'bunches', 35);

-- Create Indexes for Performance
CREATE INDEX idx_listings_product_name ON listings(product_name);
CREATE INDEX idx_listings_created_at ON listings(created_at);
CREATE INDEX idx_orders_total_price ON orders(total_price);
CREATE INDEX idx_messages_conversation ON messages(sender_id, receiver_id);