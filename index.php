<?php
$get = $_GET['get'];

// Optional flag: use proxy if needed
$useProxy = isset($_GET['proxy']) && $_GET['proxy'] == '1';

// Direct fetch from Astro
$mpdUrl = "https://linearjitp-playback.astro.com.my/dash-wv/linear/$get";
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
$mpdContent = @file_get_contents($mpdUrl, false, $context);

// If direct fetch fails or proxy flag is set, use proxy
if ($mpdContent === false || $useProxy) {
    $proxyUrl = "https://proxy.mydementiacompanion.com.au/$get";
    $mpdContent = @file_get_contents($proxyUrl);
    if ($mpdContent === false) {
        http_response_code(500);
        echo "Failed to fetch MPD from both direct and proxy sources.";
        exit;
    }
}

// Serve MPD with proper headers
header("Content-Type: application/dash+xml");
header("Accept-Ranges: bytes");
echo $mpdContent;
