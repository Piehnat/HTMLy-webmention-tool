<?php
if (!isset($_GET['source']) || empty($_GET['source'])) {
    die('Bitte die Quelle als URL-Parameter "source" angeben.');
}

$source = $_GET['source'];
$html = @file_get_contents($source);
if (!$html) {
    die("Fehler: Konnte Quelle $source nicht laden.");
}

preg_match_all('/<a\s[^>]*href=["\']([^"\']+)["\']/i', $html, $matches);
$links = array_unique($matches[1]);

echo "<h2>Webmentions senden von:</h2>";
echo "<p>$source</p>";

if (empty($links)) {
    die("Keine Links gefunden.");
}

echo "<h3>Gefundene Links:</h3><ul>";

function find_webmention_endpoint($url) {
    $headers = @get_headers($url, 1);
    if (!$headers) return false;

    if (isset($headers['Link'])) {
        $linkHeader = is_array($headers['Link']) ? implode(',', $headers['Link']) : $headers['Link'];
        if (preg_match('/<([^>]+)>;\s*rel="?webmention"?/i', $linkHeader, $m)) {
            return $m[1];
        }
    }

    $html = @file_get_contents($url);
    if ($html && preg_match('/<link[^>]+rel=["\']?webmention["\']?[^>]*href=["\']?([^"\']+)["\']?/i', $html, $m)) {
        return $m[1];
    }

    return false;
}

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
