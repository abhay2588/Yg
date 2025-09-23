<?php
$get = $_GET['get'] ?? '';

// Optional flag: force proxy
$forceProxy = isset($_GET['proxy']) && $_GET['proxy'] == '1';

// Helper function to fetch a URL with headers
function fetchUrl($url) {
    $options = [
        "http" => [
            "method" => "GET",
            "header" => 
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.5563.58 Safari/537.36\r\n" .
                "Accept: */*\r\n" .
                "Accept-Language: en-US,en;q=0.9\r\n"
        ]
    ];
    $context = stream_context_create($options);
    return @file_get_contents($url, false, $context);
}

// URLs — now defaulting to default_ott.mpd
$astroUrl = "https://linearjitp-playback.astro.com.my/dash-wv/linear/$get/default_ott.mpd";
$proxyUrl = "https://proxy.mydementiacompanion.com.au/$get/default_ott.mpd";

$mpdContent = false;
$errorMsg = [];

// 1️⃣ Force proxy if requested
if ($forceProxy) {
    $mpdContent = fetchUrl($proxyUrl);
    if ($mpdContent === false) {
        $errorMsg[] = "Proxy fetch failed: $proxyUrl";
    }
} else {
    // 2️⃣ Try direct fetch first
    $mpdContent = fetchUrl($astroUrl);
    if ($mpdContent === false) {
        $errorMsg[] = "Direct fetch failed: $astroUrl";

        // 3️⃣ Fallback to proxy if direct fails
        $mpdContent = fetchUrl($proxyUrl);
        if ($mpdContent === false) {
            $errorMsg[] = "Proxy fetch failed: $proxyUrl";
        }
    }
}

// 4️⃣ Serve or show error
if ($mpdContent !== false) {
    header("Content-Type: application/dash+xml");
    header("Accept-Ranges: bytes");
    echo $mpdContent;
} else {
    http_response_code(500);
    echo "Failed to fetch MPD.\n";
    echo implode("\n", $errorMsg);
    exit;
}
