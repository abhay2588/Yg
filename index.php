<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: *");
header("Access-Control-Allow-Methods: GET");

//
// ─── CHANNEL LIST ───────────────────────────────────────────────
//
$channels = [
    ["id" => "505", "name" => "Astro Ria", "group" => "Entertainment"],
    ["id" => "507", "name" => "Astro Prima", "group" => "Entertainment"],
    ["id" => "510", "name" => "Astro Awani", "group" => "News"],
    ["id" => "511", "name" => "Astro Oasis", "group" => "Religion"],
    ["id" => "521", "name" => "Astro Citra", "group" => "Movies"],
    ["id" => "522", "name" => "Astro BOO", "group" => "Movies"],
    ["id" => "606", "name" => "Astro Ceria", "group" => "Kids"]
];

//
// ─── GENERATE M3U PLAYLIST ───────────────────────────────────────
//
function generate_m3u($channels, $baseUrl) {
    $out = "#EXTM3U\n";
    foreach ($channels as $ch) {
        $out .= "#EXTINF:-1 tvg-id=\"{$ch['id']}\" tvg-name=\"{$ch['name']}\" group-title=\"{$ch['group']}\",{$ch['name']}\n";
        $out .= "{$baseUrl}?get={$ch['id']}\n";
    }
    return $out;
}

//
// ─── IF CHANNEL REQUESTED (?get=xxx) ─────────────────────────────
//
if (isset($_GET['get'])) {
    $get = preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['get']); // sanitize

    // Always use HLS (.m3u8)
    $hlsUrl  = "https://linearjitp-playback.astro.com.my/hls/live/{$get}/index.m3u8";

    // Output direct playable HLS link
    header("Content-Type: text/plain");
    echo $hlsUrl;
    exit;
}

//
// ─── OTHERWISE, OUTPUT FULL M3U PLAYLIST ─────────────────────────
//
header("Content-Type: application/vnd.apple.mpegurl");
$baseUrl = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}";
echo generate_m3u($channels, $baseUrl);
?>
