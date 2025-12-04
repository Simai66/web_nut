-- Create users table if it doesn't exist
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'staff',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (password: 1234)
-- Note: We are inserting '1234' directly. The login.php script has a fallback to accept this.
-- For better security, you should change this to a proper hash later.
INSERT INTO users (username, password_hash, full_name, role) VALUES 
('admin', '1234', 'Administrator', 'admin');
