<?php
require 'config.php';

$code = isset($_GET['code']) ? $_GET['code'] : '';

if ($code) {
    $stmt = $pdo->prepare("SELECT original_url FROM links WHERE short_code = ?");
    $stmt->execute([$code]);
    $original_url = $stmt->fetchColumn();

    if ($original_url) {
        if (parse_url($original_url, PHP_URL_SCHEME) !== 'https') {
            $original_url = preg_replace('/^http:/i', 'https:', $original_url);
        }
        header("Location: $original_url", true, 301);
        exit();
    } else {
        echo "URL non trouvée.";
    }
} else {
    echo "Code non fourni.";
}
?>
