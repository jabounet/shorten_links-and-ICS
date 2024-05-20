<?php
require 'config.php';

function isTokenValid($token, $pdo) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM access_tokens WHERE token = ?");
    $stmt->execute([$token]);
    return $stmt->fetchColumn() > 0;
}

// Generate ICS file
function createICS($event) {
    global $domain_name;
    $website = $domain_name;
    $icsContent = "BEGIN:VCALENDAR\r\n";
    $icsContent .= "VERSION:2.0\r\n";
    $icsContent .= "PRODID:-//JVET//JSMS//EN\r\n";
    $icsContent .= "BEGIN:VEVENT\r\n";
    $icsContent .= "UID:" . uniqid() . "@" . $website . "\r\n";
    $icsContent .= "DTSTAMP:" . gmdate('Ymd\THis\Z') . "\r\n";
    $icsContent .= "DTSTART:" . gmdate('Ymd\THis\Z', strtotime($event['start'])) . "\r\n";
    $icsContent .= "DTEND:" . gmdate('Ymd\THis\Z', strtotime($event['end'])) . "\r\n";
    $icsContent .= "SUMMARY:" . addslashes($event['summary']) . "\r\n";
    $icsContent .= "DESCRIPTION:" . addslashes($event['description']) . "\r\n";
    $icsContent .= "LOCATION:" . addslashes($event['location']) . "\r\n";
    $icsContent .= "END:VEVENT\r\n";
    $icsContent .= "END:VCALENDAR\r\n";
    return $icsContent;
}


if (
    isset($_GET['token']) && 
    isset($_GET['start']) && 
    isset($_GET['end']) && 
    isset($_GET['summary']) && 
    isset($_GET['description']) && 
    isset($_GET['location'])
) {
 
    if (!isTokenValid($_GET['token'], $pdo)) {
        die("Token d'accès invalide ou manquant.");
    }

   
    $event = [
        'start' => $_GET['start'],
        'end' => $_GET['end'],
        'summary' => $_GET['summary'],
        'description' => $_GET['description'],
        'location' => $_GET['location']
    ];

    
    $icsContent = createICS($event);
    $icsFilename = 'event_' . uniqid() . '.ics';
    

    header('Content-Type: text/calendar');
    header('Content-Disposition: attachment; filename="' . $icsFilename . '"');
    echo $icsContent;
    exit;
} else {
    die("Paramètres manquants.");
}
?>
