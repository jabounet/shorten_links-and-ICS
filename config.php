<?php
// Database informations : 
$host = '';
$db   = '';
$user = '';
$pass = '';

$charset = 'utf8mb4';
$domain_name = 'https://'.$_SERVER['HTTP_HOST'];

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check tables and create them if not present
    $tables = [
        'links' => "
            CREATE TABLE IF NOT EXISTS links (
                id INT AUTO_INCREMENT PRIMARY KEY,
                original_url VARCHAR(5000) NOT NULL,
                short_code VARCHAR(10) NOT NULL UNIQUE,
                expiration_date DATETIME DEFAULT NULL,
                user VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ",
        'access_tokens' => "
            CREATE TABLE IF NOT EXISTS access_tokens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                token VARCHAR(255) NOT NULL UNIQUE,
                user VARCHAR(255) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        );
        "
    ];

    foreach ($tables as $tableName => $createTableSQL) {
        $pdo->exec($createTableSQL);
    }

    $stmt = $pdo->prepare("DELETE FROM links WHERE expiration_date IS NOT NULL AND expiration_date < NOW()");
    $stmt->execute();
    
} catch (PDOException $e) {
    echo 'Connection failed: ' . $e->getMessage();
    exit;
}
?>
