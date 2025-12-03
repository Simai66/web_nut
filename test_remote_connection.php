<?php
$host = 'fdb1034.awardspace.net';
$dbname = '4705533_clothing';
$username = '4705533_clothing';
$password = 'Rb231023';

echo "Attempting to connect to AwardSpace DB from Localhost...\n";
echo "Host: $host\n";
echo "User: $username\n";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✅ SUCCESS! Connected to AwardSpace database successfully.\n";
} catch (PDOException $e) {
    echo "❌ FAILED! Could not connect.\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "\nAnalysis: This usually means AwardSpace blocks remote connections (which is common for free hosting).\n";
}
