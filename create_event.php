<?php
// Function to sanitize input data
require 'config.php';
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}

$root_url = $domain_name;
$base_url = $root_url."/ics_generator.php";
$api_url = $root_url."/api.php";

// Send to the API
function shortenLink($long_url, $token) {
    global $api_url;

    $data = json_encode(['url' => $long_url, 'token' => $token]);
    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => $data,
        ],
    ];
    $context  = stream_context_create($options);
    $result = file_get_contents($api_url, false, $context);
    
    if ($result === FALSE) {
        return "Erreur lors de la génération du lien raccourci.";
    }
    
    $response = json_decode($result, true);
    return $response['short_url'] ?? "Erreur: réponse inattendue de l'API.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start = sanitizeInput($_POST['start']);
    $end = sanitizeInput($_POST['end']);
    $summary = sanitizeInput($_POST['summary']);
    $description = sanitizeInput($_POST['description']);
    $location = sanitizeInput($_POST['location']);
    $token = sanitizeInput($_POST['token']);

    $query_string = http_build_query([
        'token' => $token,
        'start' => $start,
        'end' => $end,
        'summary' => $summary,
        'description' => $description,
        'location' => $location
    ]);
    $generated_link = $base_url . '?' . $query_string;

    $shortened_link = shortenLink($generated_link, $token);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un événement</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1 {
            margin-bottom: 20px;
        }
        label {
            font-weight: bold;
        }
        input[type="text"],
        input[type="datetime-local"],
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .link-container {
            margin-top: 20px;
        }
        .generated-link,
        .shortened-link {
            margin-bottom: 10px;
            font-size: 18px;
        }
        .error-message {
            color: red;
        }
    </style>
</head>
<body>
    <h1>Créer un événement</h1>
    <p>Remplissez le formulaire ci-dessous pour créer un événement et obtenir un lien vers l'événement ainsi qu'un lien raccourci.</p>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="start">Date et heure de début :</label><br>
        <input type="datetime-local" id="start" name="start" required><br>
        
        <label for="end">Date et heure de fin :</label><br>
        <input type="datetime-local" id="end" name="end" required><br>
        
        <label for="summary">Résumé :</label><br>
        <input type="text" id="summary" name="summary" required><br>
        
        <label for="description">Description :</label><br>
        <textarea id="description" name="description" required></textarea><br>
        
        <label for="location">Lieu :</label><br>
        <input type="text" id="location" name="location" required><br>

        <label for="token">Token d'accès :</label><br>
        <input type="text" id="token" name="token" required><br>
        
        <input type="submit" value="Générer le lien">
    </form>

    <?php
    if (isset($generated_link)) {
        echo "<div class='link-container'>";
        echo "<div class='generated-link'><b>Lien généré :</b> <a href=\"$generated_link\" target=\"_blank\">$generated_link</a></div>";
        
        if (isset($shortened_link)) {
            
            
            echo "<b>Lien raccourci: </b> <a href=\"$shortened_link\">$shortened_link</a> <button id=\"copyButton\" onclick=\"copyToClipboard('$shortened_link')\">Copier</button>";
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

        echo "</div>";
    }
    ?>
</body>
</html>
