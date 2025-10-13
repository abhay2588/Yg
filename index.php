<?php
// Allow cross-platform playback
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/vnd.apple.mpegurl");

// Get ?get= parameter (channel ID)
if (!isset($_GET['get'])) {
    http_response_code(400);
    exit("Missing 'get' parameter");
}

$get = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['get']); // sanitize input

// DASH and HLS URLs
$dashUrl = "https://linearjitp-playback.astro.com.my/dash-wv/linear/{$get}/manifest.mpd";
$hlsUrl  = "https://linearjitp-playback.astro.com.my/hls/live/{$get}/index.m3u8";

// Universal User-Agent for all platforms
$ua = "Mozilla/5.0 (Linux; Android 10; SmartTV; Tizen 6.5) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/111.0.5563.58 Safari/537.36";

$opts = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: {$ua}\r\nAccept: */*\r\nConnection: close\r\n",
        "timeout" => 10,
        "ignore_errors" => true
    ]
];

$context = stream_context_create($opts);

// Try DASH first
$dash = @file_get_contents($dashUrl, false, $context);

if ($dash && stripos($dash, '<MPD') !== false) {
    header("Content-Type: application/dash+xml");
    echo $dash;
    exit;
}

// If DASH fails, fallback to HLS
$hls = @file_get_contents($hlsUrl, false, $context);

if ($hls && stripos($hls, '#EXTM3U') !== false) {
    header("Content-Type: application/vnd.apple.mpegurl");
    echo $hls;
    exit;
}

// If both fail, return error
http_response_code(502);
exit("Stream not available or invalid channel ID");
?>
