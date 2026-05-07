-- Q8 Portal Database
-- This is intentionally vulnerable. Do not deploy to production.

USE q8portal;

-- =====================================================================
-- USERS table — contains Q8 staff accounts
-- The `password` column stores bcrypt hashes (cost factor 10).
-- Ahmed's hash here is the same one students extract via SQLi on Day 4
-- and crack on Day 7 with Hashcat. Cracks to "Layan@2017".
-- =====================================================================
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(100),
  full_name VARCHAR(100),
  role VARCHAR(50),
  department VARCHAR(50),
  joined DATE
);

INSERT INTO users (username, password, email, full_name, role, department, joined) VALUES
  ('admin',   '$2b$10$nx1P7AHZdo5pafBK1uV3QOhK9qyR32iO2Tg9gc04b.C9T8CaY3/rC', 'admin@q8logistics.com',           'System Administrator',    'Admin',              'IT',         '2014-10-01'),
  ('yousef',  '$2b$10$CBinBOf74qoNQdYFI43HFeinHow7LN2XLI9KavGfYrm09tzrF/rQK', 'yousef@q8logistics.com',          'Yousef Al-Mutawa',        'CEO',                'Executive',  '2014-10-01'),
  ('sarah',   '$2b$10$xmdpTVpJx9fHyh.gIgHRHuTll93CywbdBhJ8K4jwig78aahApRozu', 'sarah.alkhalid@q8logistics.com',  'Sarah Al-Khalid',         'Marketing Manager',  'Marketing',  '2018-03-15'),
  ('ahmed',   '$2b$10$/aRi2O42f2JtOUjwYwv/j.p.fYi14GCS6fQJYNaV9TX/QrHyEoy5G', 'ahmed.alrashid@q8logistics.com',  'Ahmed Al-Rashid',         'IT Director',        'IT',         '2018-03-15'),
  ('reem',    '$2b$10$/ZvO/WrXFPJxjv807rPAnex4z/.ToCWgjaPdssS9q3/EfPJLQy1AO', 'reem.alhajri@q8logistics.com',    'Reem Al-Hajri',           'Marketing Lead',     'Marketing',  '2020-06-01'),
  ('faisal',  '$2b$10$xKHVOj2AW0ZLwtetoxvS2.iDcQIulIaPPKvpPBuR/xJHPHbPGoUSK', 'faisal.alanjari@q8logistics.com', 'Faisal Al-Anjari',        'Head of Operations', 'Operations', '2017-09-15');

-- =====================================================================
-- PRODUCTS table — Q8's logistics services
-- Has a vulnerable ?id=N parameter (SQLi via products.php)
-- 3 displayable columns (name, price, category) → match for UNION SELECT
-- =====================================================================
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100),
  price DECIMAL(10,2),
  category VARCHAR(50),
  description TEXT
);

INSERT INTO products (name, price, category, description) VALUES
  ('Express Same-Day Delivery',    25.00,  'Domestic Logistics', 'Same-day shipment within Kuwait City. Up to 5kg.'),
  ('Standard Delivery (3 days)',   8.00,   'Domestic Logistics', 'Standard 3-business-day delivery within Kuwait.'),
  ('GCC Cross-Border (KSA)',       45.00,  'International',      'Door-to-door delivery from Kuwait to Saudi Arabia. 2-4 days.'),
  ('GCC Cross-Border (UAE)',       55.00,  'International',      'Door-to-door delivery from Kuwait to UAE. 3-5 days.'),
  ('Bulk Cargo (per pallet)',      120.00, 'Commercial',         'Pallet shipping for businesses. Includes loading.'),
  ('Cold Chain (refrigerated)',    180.00, 'Specialized',        'Temperature-controlled shipping for perishables and pharma.'),
  ('White-Glove Furniture Move',   95.00,  'Specialized',        'Furniture pickup and assembly. 2-person team.'),
  ('Document Courier',             6.00,   'Domestic Logistics', 'Same-day envelope delivery. Up to 500g.');

-- =====================================================================
-- SETTINGS table — used as a hidden gem for the flag
-- Students who enumerate via information_schema find this and extract the flag.
-- =====================================================================
CREATE TABLE settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  config_key VARCHAR(100),
  config_value VARCHAR(255),
  notes TEXT
);

INSERT INTO settings (config_key, config_value, notes) VALUES
  ('site_name',           'Q8 Logistics Portal',                  'Public site name'),
  ('admin_contact',       'admin@q8logistics.com',                'Internal admin contact'),
  ('maintenance_window',  'Sunday 02:00-04:00 UTC',               'Scheduled downtime'),
  ('flag',                'FLAG{ahmeds_hash_belongs_to_us}',      'Day 4 capture flag — proof of database compromise'),
  ('backup_location',     '/var/backups/q8-mysqldump-daily.sql',  'Daily DB backup path');
