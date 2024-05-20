<?php
require 'config.php';
function shortenLink($original_url, $token, $never_expires = false) {
    global $domain_name;
    $website = $domain_name;
    $api_url = $domain_name."/api";

    $data = [
        'url' => $original_url,
        'token' => $token,
        'never_expires' => $never_expires
    ];

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($api_url, false, $context);

    if ($result === FALSE) {
        return "Erreur lors de la génération du lien raccourci.";
    }

    $response = json_decode($result, true);
    //echo $result;
    if (json_last_error() !== JSON_ERROR_NONE) {
        return "Erreur: réponse inattendue de l'API (JSON invalide).";
    }

    return $response['short_url'] ?? $response['error'] ?? "Erreur: réponse inattendue de l'API.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $original_url = $_POST['url'];
    $token = $_POST['token']; 
    $never_expires = isset($_POST['never_expires']) && $_POST['never_expires'] == '1';

    if (!filter_var($original_url, FILTER_VALIDATE_URL)) {
        die("URL invalide");
    }

    $shortened_link = shortenLink($original_url, $token, $never_expires);

    
    echo "Lien raccourci : <a href=\"$shortened_link\">$shortened_link</a> <button id=\"copyButton\" onclick=\"copyToClipboard('$shortened_link')\">Copier</button>";
    echo "<script>
    function copyToClipboard(text) {
        var textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        
        var copyButton = document.getElementById('copyButton');
        copyButton.disabled = true;
        copyButton.textContent = 'Copié';
    }
    </script>";
    
}
?>
