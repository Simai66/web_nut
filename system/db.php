<?php
// Database Connection
// Check if running on localhost
// Check if running on localhost or CLI
$is_localhost = false;
if (php_sapi_name() === 'cli' || (isset($_SERVER['HTTP_HOST']) && (strpos($_SERVER['HTTP_HOST'], 'localhost') !== false || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false))) {
    $is_localhost = true;
}

if ($is_localhost) {
    // Localhost (MAMP/XAMPP)
    $host = '127.0.0.1'; // Use IP to avoid socket issues

    // List of possible local database names to try
    $possible_db_names = ['clothing_store', 'web_nut', '4705533_clothing'];
    $possible_ports = [8889, 3306]; // MAMP (8889), XAMPP (3306)
    $possible_passwords = ['root', '']; // MAMP ('root'), XAMPP ('')

    $pdo = null;
    $connected = false;
    $errors = [];

    foreach ($possible_ports as $port) {
        foreach ($possible_db_names as $dbname) {
            foreach ($possible_passwords as $password) {
                if ($connected) break;

                try {
                    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
                    if ($is_localhost && file_exists('/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock')) {
                        $dsn .= ";unix_socket=/Applications/XAMPP/xamppfiles/var/mysql/mysql.sock";
                    }
                    $pdo = new PDO($dsn, 'root', $password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $connected = true;
                    // echo "Connected to $dbname on port $port"; // Debug
                } catch (PDOException $e) {
                    $errors[] = "Port $port, DB $dbname: " . $e->getMessage();
                }
            }
        }
    }

    if (!$connected) {
        die("<h3>Local Database Connection Failed</h3>
         <p>Could not connect to any local database.</p>
         <p><strong>Tried:</strong> Ports 8889/3306, DBs: " . implode(', ', $possible_db_names) . "</p>
         <p><strong>Last Error:</strong> " . end($errors) . "</p>
         <p><strong>Action:</strong> Check your MAMP/XAMPP status and ensure one of these databases exists.</p>");
    }
} else {
    // AwardSpace (Remote)
    $host = 'fdb1034.awardspace.net';
    $dbname = '4705533_clothing';
    $username = '4705533_clothing';
    $password = 'Rb231023';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}

// Start Session
session_start();

// Helper Function for Login Check
function checkLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}
