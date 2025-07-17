<?php
// Einfaches PHP-Webmention-Sender-Skript mit Passwortschutz, cURL, Delay & IP-Logging

// 🔐 Passwortschutz aktivieren
$authKey = 'mein-geheimer-code'; // <- Hier dein Passwort reinschreiben
if (!isset($_GET['auth']) || $_GET['auth'] !== $authKey) {
    // Zugriff loggen
    file_put_contents('webmention-log.txt', date('Y-m-d H:i:s') . " - FAILED access from IP: " . $_SERVER['REMOTE_ADDR'] . "\n", FILE_APPEND);
    die('Unauthorized access');
}

// ✅ Erfolgreichen Zugriff loggen
file_put_contents('webmention-log.txt', date('Y-m-d H:i:s') . " - Access from IP: " . $_SERVER['REMOTE_ADDR'] . "\n", FILE_APPEND);

// 🕒 Anti-Brute-Force: kleine Verzögerung
sleep(2);

if (!isset($_GET['source']) || empty($_GET['source'])) {
    die('Bitte die Quelle als URL-Parameter "source" angeben.');
}

$source = $_GET['source'];

// cURL-Funktion zum Laden von URLs
function curl_get_contents($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Redirects folgen
    curl_setopt($ch, CURLOPT_USERAGENT, 'Webmention Sender by Piehnat'); // User-Agent setzen
    curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Timeout 10 Sekunden
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

// 1. Lade den Inhalt der Quellseite via cURL
$html = curl_get_contents($source);
if (!$html) {
    die("Fehler: Konnte Quelle $source nicht laden.");
}

// 2. Alle Links aus der Quelle extrahieren (einfach per Regex)
preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\']/i', $html, $matches);
$links = array_unique($matches[1]);

echo "<h2>Webmentions senden von:</h2>";
echo "<p>$source</p>";

if (empty($links)) {
    die("Keine Links gefunden.");
}

echo "<h3>Gefundene Links:</h3><ul>";

// Hilfsfunktion: Webmention-Endpunkt finden (mit cURL)
function find_webmention_endpoint($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_NOBODY, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Webmention Sender by Piehnat');
    $headers = curl_exec($ch);
    curl_close($ch);

    if (!$headers) return false;

    if (preg_match_all('/Link:\s*<([^>]+)>;\s*rel="?webmention"?/i', $headers, $matches)) {
        return $matches[1][0];
    }

    $html = curl_get_contents($url);
    if ($html && preg_match('/<link[^>]+rel=["\']?webmention["\']?[^>]*href=["\']?([^"\']+)["\']?/i', $html, $m)) {
        return $m[1];
    }

    return false;
}

// Hilfsfunktion: Webmention senden
function send_webmention($source, $target, $endpoint) {
    $data = http_build_query([
        'source' => $source,
        'target' => $target
    ]);

    $opts = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => $data,
            'ignore_errors' => true
        ]
    ];
    $context = stream_context_create($opts);
    $result = @file_get_contents($endpoint, false, $context);
    return $result !== false;
}

// 3. Für jeden Link prüfen wir den Webmention-Endpunkt und senden, falls vorhanden
foreach ($links as $link) {
    echo "<li>$link — ";
    $endpoint = find_webmention_endpoint($link);
    if ($endpoint) {
        $ok = send_webmention($source, $link, $endpoint);
        echo $ok ? "<strong>Webmention gesendet</strong>" : "<strong>Fehler beim Senden</strong>";
    } else {
        echo "<em>Kein Webmention-Endpunkt gefunden</em>";
    }
    echo "</li>";
}

echo "</ul>";
?>
