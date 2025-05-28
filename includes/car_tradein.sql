CREATE DATABASE IF NOT EXISTS car_tradein;
USE car_tradein;

-- Drop existing tables if they exist
-- Users table (car owners / traders)
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  phone VARCHAR(20),
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Dealers table (car dealers)
CREATE TABLE dealers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  business_name VARCHAR(150) NOT NULL,
  contact_person VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  phone VARCHAR(20),
  password VARCHAR(255) NOT NULL,
  kra_pin VARCHAR(20),
  approved BOOLEAN DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Cars table (listed cars for trade or sale)
CREATE TABLE cars (
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
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (owner_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (dealer_id) REFERENCES dealers(id) ON DELETE SET NULL
);

-- Car photos (multiple photos per car)
CREATE TABLE car_photos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  car_id INT NOT NULL,
  photo_path VARCHAR(255) NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
);

-- Trades table (car trade-in deals)
CREATE TABLE trades (
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

-- Offers table (individual trade offers from dealers to users)
CREATE TABLE offers (
  id INT AUTO_INCREMENT PRIMARY KEY,
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

-- Messages between users and dealers
CREATE TABLE messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sender_type ENUM('user','dealer','admin') NOT NULL,
  sender_id INT NOT NULL,
  receiver_type ENUM('user','dealer','admin') NOT NULL,
  receiver_id INT NOT NULL,
  trade_id INT DEFAULT NULL,
  message TEXT NOT NULL,
  sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Documents uploaded by users (logbook, NTSA check, service history)
CREATE TABLE documents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  car_id INT NOT NULL,
  doc_type ENUM('logbook','ntsa_check','service_history') NOT NULL,
  file_path VARCHAR(255) NOT NULL,
  uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (car_id) REFERENCES cars(id) ON DELETE CASCADE
);

-- Finance Partners and Plans
CREATE TABLE finance_partners (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  contact_info TEXT,
  website VARCHAR(255)
);

CREATE TABLE finance_plans (
  id INT AUTO_INCREMENT PRIMARY KEY,
  partner_id INT NOT NULL,
  plan_name VARCHAR(100) NOT NULL,
  interest_rate DECIMAL(5,2) NOT NULL,
  repayment_period_months INT NOT NULL,
  details TEXT,
  FOREIGN KEY (partner_id) REFERENCES finance_partners(id) ON DELETE CASCADE
);

-- Admin users table
CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  full_name VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Settings table for platform configs
CREATE TABLE settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  config_key VARCHAR(100) UNIQUE NOT NULL,
  config_value TEXT NOT NULL
);
