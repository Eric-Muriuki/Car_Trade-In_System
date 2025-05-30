-- Create and use the database
CREATE DATABASE IF NOT EXISTS car_tradein;
USE car_tradein;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    user_type ENUM('admin', 'user') DEFAULT 'user',
    is_blocked TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Dealers table
CREATE TABLE IF NOT EXISTS dealers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    business_name VARCHAR(150) NOT NULL,
    contact_person VARCHAR(100) NOT NULL,
    address TEXT,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(20),
    password VARCHAR(255) NOT NULL,
    kra_pin VARCHAR(20),
    approved BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cars table
CREATE TABLE IF NOT EXISTS cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    owner_id INT NOT NULL,
    dealer_id INT DEFAULT NULL,
    make VARCHAR(50) NOT NULL,
    model VARCHAR(50) NOT NULL,
    year YEAR NOT NULL,
    car_condition ENUM('new','used','fair') NOT NULL,
    mileage INT DEFAULT 0,
    image VARCHAR(255),
    price DECIMAL(12,2) NOT NULL,
    description TEXT,
    status ENUM('available','traded','sold') DEFAULT 'available',
    is_approved TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (dealer_id) REFERENCES dealers(id) ON DELETE SET NULL
);

-- Car photos
CREATE TABLE IF NOT EXISTS car_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT NOT NULL,
    photo_path VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
);

-- Trades
CREATE TABLE IF NOT EXISTS trades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    dealer_id INT NOT NULL,
    user_car_id INT NOT NULL,
    dealer_car_id INT DEFAULT NULL,
    offer_price DECIMAL(12,2) NOT NULL,
    status ENUM('pending','accepted','declined','completed','cancelled') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (dealer_id) REFERENCES dealers(id) ON DELETE CASCADE,
    FOREIGN KEY (user_car_id) REFERENCES cars(id) ON DELETE CASCADE,
    FOREIGN KEY (dealer_car_id) REFERENCES cars(id) ON DELETE SET NULL
);

-- Offers
CREATE TABLE IF NOT EXISTS offers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    car_id INT NOT NULL,
    trade_id INT NOT NULL,
    dealer_id INT NOT NULL,
    offer_price DECIMAL(12,2) NOT NULL,
    message TEXT,
    status ENUM('pending','accepted','declined','countered') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (trade_id) REFERENCES trades(id) ON DELETE CASCADE,
    FOREIGN KEY (dealer_id) REFERENCES dealers(id) ON DELETE CASCADE
);

-- Messages (Fixed incorrect table name reference)
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_type ENUM('user', 'dealer', 'admin') NOT NULL,
    sender_id INT NOT NULL,
    receiver_type ENUM('user', 'dealer', 'admin') NOT NULL,
    receiver_id INT NOT NULL,
    trade_id INT NULL,
    message TEXT NOT NULL,
    sent_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trade_id) REFERENCES trades(id) ON DELETE CASCADE
);

-- Support tickets
CREATE TABLE IF NOT EXISTS support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    dealer_id INT NULL,
    subject VARCHAR(255) NOT NULL,
    status ENUM('Open', 'Pending', 'Closed') DEFAULT 'Open',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (dealer_id) REFERENCES dealers(id) ON DELETE CASCADE
);

-- Documents
CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    car_id INT NOT NULL,
    logbook VARCHAR(255) NOT NULL,
    ntsa_results VARCHAR(255) NOT NULL,
    service_history VARCHAR(255) NOT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
);

-- Finance partners
CREATE TABLE IF NOT EXISTS finance_partners (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    contact_info TEXT,
    website VARCHAR(255),
    logo VARCHAR(255),         -- Added for logo filename/path
    description TEXT           -- Added for partner description
);

CREATE TABLE IF NOT EXISTS finance_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    partner_id INT NOT NULL,
    plan_name VARCHAR(100) NOT NULL,
    interest_rate DECIMAL(5,2) NOT NULL,
    duration_months INT NOT NULL,   -- renamed from repayment_period_months
    min_amount DECIMAL(15,2),       -- added to match PHP usage
    max_amount DECIMAL(15,2),       -- added to match PHP usage
    details TEXT,
    FOREIGN KEY (partner_id) REFERENCES finance_partners(id) ON DELETE CASCADE
);


-- Admin users
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Settings
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    config_key VARCHAR(100) UNIQUE NOT NULL,
    config_value TEXT NOT NULL
); 

-- Insert admin
INSERT INTO admins (name, email, password)
VALUES ('Admin User', 'admin@swapride.co.ke', '$2y$10$EXAMPLEHASHEDPASSWORD');

-- Insert users
INSERT INTO users (full_name, email, phone, password)
VALUES 
('John Doe', 'john@example.com', '0712345678', '$2y$10$EXAMPLEHASHEDPASSWORD'),
('Jane Smith', 'jane@example.com', '0723456789', '$2y$10$EXAMPLEHASHEDPASSWORD');

-- Insert dealers
INSERT INTO dealers (business_name, contact_person, address, email, phone, password, kra_pin, approved)
VALUES 
('Nairobi Auto Traders', 'Michael Kimani', 'Ngong Road, Nairobi', 'dealer1@swapride.co.ke', '0798765432', '$2y$10$EXAMPLEHASHEDPASSWORD', 'A123456789B', TRUE),
('Mombasa Car Bazaar', 'Salma Noor', 'Moi Avenue, Mombasa', 'dealer2@swapride.co.ke', '0787654321', '$2y$10$EXAMPLEHASHEDPASSWORD', 'B987654321A', FALSE);

-- Insert cars
INSERT INTO cars (owner_id, dealer_id, make, model, year, car_condition, mileage, image, price, description, status, is_approved)
VALUES 
(1, 1, 'Toyota', 'Corolla', 2016, 'used', 78000, 'images/cars/corolla.jpg', 1200000.00, 'Reliable and well-maintained.', 'available', 1),
(2, NULL, 'Mazda', 'CX-5', 2020, 'new', 15000, 'images/cars/cx5.jpg', 2500000.00, 'Like new, top condition.', 'available', 0);

-- Insert car photos
INSERT INTO car_photos (car_id, photo_path)
VALUES 
(1, 'images/cars/corolla_1.jpg'),
(1, 'images/cars/corolla_2.jpg'),
(2, 'images/cars/cx5_1.jpg');

-- Insert trades
INSERT INTO trades (user_id, dealer_id, user_car_id, dealer_car_id, offer_price)
VALUES 
(1, 1, 1, NULL, 1100000.00);

-- Insert offers
INSERT INTO offers (trade_id, dealer_id, offer_price, message)
VALUES 
(1, 1, 1150000.00, 'We are offering a slightly higher amount to close the deal quickly.');

-- Insert messages
INSERT INTO messages (sender_type, sender_id, receiver_type, receiver_id, trade_id, message)
VALUES 
('user', 1, 'dealer', 1, 1, 'I’m interested in the trade. Can we negotiate the offer price?'),
('dealer', 1, 'user', 1, 1, 'Sure, we’re open to discussion. Let’s find a fair deal.');

-- Insert support tickets
INSERT INTO support_tickets (user_id, subject)
VALUES 
(2, 'Issue uploading car documents');

-- Insert documents
INSERT INTO documents (user_id, car_id, logbook, ntsa_results, service_history)
VALUES 
(1, 1, 'docs/logbook_john.pdf', 'docs/ntsa_john.pdf', 'docs/service_john.pdf');

-- Insert finance partners
INSERT INTO finance_partners (name, contact_info, website, logo, description)
VALUES 
('Equity Bank', '020-1234567', 'https://equitybank.co.ke', 'logos/equity.png', 'Affordable car financing plans.'),
('NCBA Bank', '020-7654321', 'https://ncbagroup.com', 'logos/ncba.png', 'Drive your dream car with flexible repayment options.');

-- Insert finance plans
INSERT INTO finance_plans (partner_id, plan_name, interest_rate, duration_months, min_amount, max_amount, details)
VALUES 
(1, 'Standard Auto Loan', 13.5, 48, 500000, 5000000, 'Flexible repayments, early settlement allowed.'),
(2, 'Zero Deposit Plan', 16.0, 60, 1000000, 7000000, 'Drive now, pay later with zero deposit.');

-- Insert settings
INSERT INTO settings (config_key, config_value)
VALUES 
('site_name', 'SwapRide Kenya'),
('support_email', 'support@swapride.co.ke'),
('maintenance_mode', 'off');

