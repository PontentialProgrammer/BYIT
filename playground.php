---
tags: [php, mysql, pdo, database, tutorial]
---

# 📄 One-File PDO Playground — PHP + MySQL

## Overview
This is a **self-contained** PHP script to:
1. Connect to MySQL (PDO)
2. Create a database if it doesn't exist
3. Create a `users` table
4. Insert a test user
5. Fetch users
6. Update a user
7. Delete a user

---

## Code
```php
<?php
// ---------- CONFIG ----------
$host = "localhost";
$dbname = "pdo_demo";
$user = "root";
$pass = "";

// ---------- CONNECT & CREATE DB ----------
try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create DB if not exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    echo "✅ Database checked/created.<br>";

    // Switch to DB
    $pdo->exec("USE $dbname");

    // Create table if not exists
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(50) NOT NULL,
            password VARCHAR(255) NOT NULL
        )
    ");
    echo "✅ Table checked/created.<br>";

} catch (PDOException $e) {
    die("❌ Connection failed: " . $e->getMessage());
}

// ---------- INSERT ----------
try {
    $stmt = $pdo->prepare("INSERT INTO users (username, password) VALUES (:username, :password)");
    $stmt->execute([
        ":username" => "JohnDoe",
        ":password" => password_hash("mypassword", PASSWORD_DEFAULT)
    ]);
    echo "✅ User inserted.<br>";
} catch (PDOException $e) {
    echo "⚠️ Insert error: " . $e->getMessage() . "<br>";
}

// ---------- SELECT ----------
try {
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "<pre>📋 Users:\n";
    print_r($users);
    echo "</pre>";
} catch (PDOException $e) {
    echo "⚠️ Select error: " . $e->getMessage() . "<br>";
}

// ---------- UPDATE ----------
try {
    $stmt = $pdo->prepare("UPDATE users SET username = :newname WHERE username = :oldname");
    $stmt->execute([
        ":newname" => "JaneDoe",
        ":oldname" => "JohnDoe"
    ]);
    echo "✅ User updated.<br>";
} catch (PDOException $e) {
    echo "⚠️ Update error: " . $e->getMessage() . "<br>";
}

// ---------- DELETE ----------
try {
    $stmt = $pdo->prepare("DELETE FROM users WHERE username = :username");
    $stmt->execute([":username" => "JaneDoe"]);
    echo "✅ User deleted.<br>";
} catch (PDOException $e) {
    echo "⚠️ Delete error: " . $e->getMessage() . "<br>";
}

?>
