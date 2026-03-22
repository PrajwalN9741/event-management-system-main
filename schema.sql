-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(80) UNIQUE NOT NULL,
    email VARCHAR(120) UNIQUE NOT NULL,
    password_hash VARCHAR(256) NOT NULL,
    role VARCHAR(20) DEFAULT 'staff',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Events table
CREATE TABLE IF NOT EXISTS events (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(200) NOT NULL,
    event_type VARCHAR(50) NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NULL,
    venue VARCHAR(300) NOT NULL,
    client_name VARCHAR(150) NOT NULL,
    client_email VARCHAR(120) NULL,
    client_phone VARCHAR(20) NULL,
    notes TEXT NULL,
    status VARCHAR(30) DEFAULT 'confirmed',
    flower_items_json TEXT DEFAULT '[]',
    created_by INTEGER NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Inventory items table
CREATE TABLE IF NOT EXISTS inventory_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(150) NOT NULL,
    category VARCHAR(80) NULL,
    quantity REAL DEFAULT 0.0,
    unit VARCHAR(30) DEFAULT 'pcs',
    price_per_unit REAL DEFAULT 0.0,
    low_stock_threshold REAL DEFAULT 10.0,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Event–Inventory Junction table
CREATE TABLE IF NOT EXISTS event_inventory (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    event_id INTEGER NOT NULL,
    item_id INTEGER NOT NULL,
    quantity_used REAL NOT NULL DEFAULT 0.0,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    FOREIGN KEY (item_id) REFERENCES inventory_items(id)
);

-- Quotations table
CREATE TABLE IF NOT EXISTS quotations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    event_id INTEGER NOT NULL,
    pdf_path VARCHAR(300) NULL,
    total_amount REAL DEFAULT 0.0,
    generated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE
);

-- Seed admin user (password: Admin@123)
-- Hash generated via password_hash('Admin@123', PASSWORD_DEFAULT)
INSERT OR IGNORE INTO users (username, email, password_hash, role) 
VALUES ('admin', 'admin@ems.local', '$2y$10$0s0XWCrfwXIZpYzAUvOJf.NmJ6NJ4.abBRAE/syWUonzXq05DJh7i', 'admin');
