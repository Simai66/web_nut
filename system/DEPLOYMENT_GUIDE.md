# Deployment Guide for Clothery System on AwardSpace

This guide will help you deploy your Clothery System to AwardSpace hosting.

## Prerequisites

1.  **AwardSpace Account**: You should have access to your AwardSpace control panel.
2.  **Database Credentials**:
    *   Host: `fdb1034.awardspace.net`
    *   Database: `4705533_clothing`
    *   User: `4705533_clothing`
    *   Password: `Rb231023`
3.  **Files**: All files in the `system` folder are ready.

## Step 1: Upload Files

1.  Log in to your AwardSpace Control Panel.
2.  Go to **File Manager**.
3.  Navigate to your domain's public folder (usually `yourdomain.com` or `public_html`).
4.  Create a new folder named `system` (or upload directly if you prefer).
5.  Upload **ALL** files from your local `system` folder to this directory on the server.
    *   `login.php`, `dashboard.php`, `pos.php`, `products.php`, `orders.php`, `save_order.php`, `logout.php`, `db.php`, `sidebar.php`, `style.css`
    *   Ensure `setup_users.sql` is also uploaded (optional, but good for reference).

## Step 2: Import Database Schema

1.  In AwardSpace Control Panel, go to **PHPMyAdmin**.
2.  Log in to your database (`4705533_clothing`).
3.  Click on the **Import** tab.
4.  Choose the file `4705533_clothing.sql` (the one you provided earlier) and click **Go**.
    *   *Note: If you don't have this file handy, ensure your tables (`products`, `orders`, `order_items`, `customers`) exist.*

## Step 3: Create Users Table

1.  In PHPMyAdmin, click on the **SQL** tab.
2.  Copy and paste the content of `setup_users.sql` into the text box:
    ```sql
    CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        full_name VARCHAR(100) NOT NULL,
        role ENUM('admin', 'staff') DEFAULT 'staff',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    INSERT INTO users (username, password_hash, full_name, role) VALUES 
    ('admin', '1234', 'Administrator', 'admin');
    ```
3.  Click **Go**.

## Step 4: Test the System

1.  Open your browser and navigate to:
    `http://<your-domain>/system/login.php`
2.  Login with:
    *   Username: `admin`
    *   Password: `1234`
3.  If successful, you should be redirected to the Dashboard.

## Troubleshooting

*   **Database Connection Error**: Double-check `db.php` on the server. Ensure the password is correct.
*   **Login Failed**: If `admin` / `1234` doesn't work, verify the `users` table in PHPMyAdmin. You can try manually inserting a row if the SQL script failed.
*   **404 Not Found**: Ensure you uploaded the files to the correct directory and are accessing the correct URL.
