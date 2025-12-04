<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>System Check</h1>";
echo "<p><strong>PHP Version:</strong> " . phpversion() . "</p>";
echo "<p><strong>Server Software:</strong> " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
echo "<p><strong>Document Root:</strong> " . $_SERVER['DOCUMENT_ROOT'] . "</p>";

echo "<h2>Database Connection Check</h2>";

$hosts = ['127.0.0.1', 'localhost'];
$possible_db_names = ['clothing_store', 'web_nut', '4705533_clothing'];
$possible_ports = [3306, 8889];
$possible_passwords = ['root', ''];

$connected = false;

foreach ($hosts as $host) {
    foreach ($possible_ports as $port) {
        foreach ($possible_db_names as $dbname) {
            foreach ($possible_passwords as $password) {
                if ($connected) break;

                try {
                    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8";
                    $pdo = new PDO($dsn, 'root', $password);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    $connected = true;
                    echo "<p style='color:green'>✅ <strong>Success!</strong> Connected to database '<strong>$dbname</strong>' on host <strong>$host</strong> port <strong>$port</strong>.</p>";
                } catch (PDOException $e) {
                    echo "<div style='font-size:12px; color:#666;'>Failed: Host $host, Port $port, DB $dbname, User root, Pass '$password' <br>Error: " . $e->getMessage() . "</div><br>";
                }
            }
        }
    }
}

if (!$connected) {
    echo "<p style='color:red; font-weight:bold; font-size:18px;'>❌ Failed! Could not connect to any local database.</p>";
    echo "<p><strong>Common Fixes:</strong></p>";
    echo "<ul>";
    echo "<li>Check if your MAMP MySQL server is running (Green light).</li>";
    echo "<li>Check your Database Name in PHPMyAdmin. Is it one of: " . implode(', ', $possible_db_names) . "?</li>";
    echo "</ul>";
}

echo "<h2>File Check</h2>";
if (file_exists('system/login.php')) {
    echo "<p>✅ system/login.php found.</p>";
    echo "<p><a href='system/login.php' style='font-size:20px; font-weight:bold;'>Click here to go to Login Page</a></p>";
} else {
    echo "<p style='color:red'>❌ system/login.php NOT found.</p>";
}
