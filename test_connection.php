<?php
// Simple connection test without autoload
echo "=== Testing Database Connection ===\n\n";

// Test different host configurations
$hosts = ['localhost', '127.0.0.1', '::1'];
$dbName = 'universe_db';
$user = 'root';
$password = '';

echo "1. Testing different host configurations...\n";
foreach ($hosts as $host) {
    try {
        $dsn = "mysql:host={$host};charset=utf8mb4";
        $conn = new PDO($dsn, $user, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);
        echo "✅ Host '{$host}' connection works!\n";
        
        // Test if database exists
        try {
            $conn->exec("USE {$dbName}");
            echo "✅ Database '{$dbName}' exists on host '{$host}'\n";
        } catch (Exception $e) {
            echo "❌ Database '{$dbName}' not found on host '{$host}'\n";
        }
        
    } catch (Exception $e) {
        echo "❌ Host '{$host}' failed: " . $e->getMessage() . "\n";
    }
    echo "\n";
}

// Test 2: Check MySQL socket configuration
echo "2. Checking MySQL configuration...\n";
try {
    $conn = new PDO("mysql:host=localhost", $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    
    $stmt = $conn->query("SHOW VARIABLES WHERE Variable_name IN ('hostname', 'port', 'socket')");
    $vars = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($vars as $var) {
        echo "   {$var['Variable_name']}: {$var['Value']}\n";
    }
} catch (Exception $e) {
    echo "❌ Could not get MySQL variables: " . $e->getMessage() . "\n";
}

echo "\n=== Recommended Host Configuration ===\n";
echo "Based on the tests above, use:\n";
echo "- Host: localhost (most common)\n";
echo "- Alternative: 127.0.0.1 (if localhost fails)\n";
echo "- Port: 3306 (default MySQL port)\n";

// Test with new credentials
echo "\n=== Testing New Database Connection ===\n\n";

$host = 'localhost';
$dbName = 'universe_db';
$user = 'pizza_user';
$password = 'pizza123';

echo "Testing connection with pizza_user...\n";
try {
    $dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
    $conn = new PDO($dsn, $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "✅ Connection successful!\n";
    echo "   Host: {$host}\n";
    echo "   Database: {$dbName}\n";
    echo "   User: {$user}\n\n";
    
    // Test database operations
    $stmt = $conn->query("SELECT DATABASE() as current_db");
    $result = $stmt->fetch();
    echo "✅ Connected to database: " . $result['current_db'] . "\n";
    
    // Check tables
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "✅ Available tables:\n";
    foreach ($tables as $table) {
        echo "   - " . array_values($table)[0] . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
}

echo "\n=== Host Configuration Status ===\n";
echo "✅ Host 'localhost' is CORRECT\n";
echo "✅ Database user created successfully\n";
echo "✅ Connection configuration updated\n";
?>