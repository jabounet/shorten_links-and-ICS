<?php
header('Content-Type: application/json');
require 'config.php';

// Generate short code
function generateShortCode($length = 6) {
    $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $shortCode = '';
    for ($i = 0; $i < $length; $i++) {
        $shortCode .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $shortCode;
}

// Check access token
function isTokenValid($token, $pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM access_tokens WHERE token = ?");
    $stmt->execute([$token]);
    return $stmt->fetchColumn() > 0;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['token']) || !isTokenValid($data['token'], $pdo)) {
        echo json_encode(['error' => 'Token d\'accès invalide ou manquant. '.$data['token']]);
        exit;
    }
    $token = $data['token'];

    if (isset($data['url'])) {
        // && filter_var($data['url'], FILTER_VALIDATE_URL)
        $original_url = $data['url'];


// Check if the original URL already exists
$stmt = $pdo->prepare("SELECT short_code FROM links WHERE original_url = ?");
$stmt->execute([$original_url]);
$existing_link = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing_link) {
    // Return the existing short code
    echo json_encode(['short_url' => $domain_name . "/"  . $existing_link['short_code']]);
} else {



        $short_code = generateShortCode();
        $never_expires = isset($data['never_expires']) && $data['never_expires'] == true;
        $expiration_date = $never_expires ? null : date('Y-m-d H:i:s', strtotime('+30 days'));
        $sql = "
        INSERT INTO links (original_url, short_code, expiration_date, user)
        SELECT ?, ?, ?, user
        FROM access_tokens
        WHERE token = ?
        ";
        $stmt = $pdo->prepare($sql);


        if ($stmt->execute([$original_url, $short_code, $expiration_date, $token])) {
            $shortUrl =$domain_name . '/' . $short_code;
           
            echo json_encode(['short_url' => $shortUrl]);
        } else {
            echo json_encode(['error' => 'Erreur lors de la sauvegarde de l\'URL.']);
        }
    }
    } else {
        echo json_encode(['error' => 'URL ou données d\'événement manquantes.']);
    }
} else {
    echo json_encode(['error' => 'Méthode de requête non valide.']);
}
?>
