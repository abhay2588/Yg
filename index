<?php
$get = $_GET['get'];
$mpdUrl = 'https://linearjitp-playback.astro.com.my/dash-wv/linear/' . $get;

// Multi-platform User-Agent
$ua = "Mozilla/5.0 (Windows NT 10.0; Win64; x64; Linux; Android 10; MI 9 Build/QKQ1.190825.002; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/111.0.5563.58 Mobile Safari/537.36";

$mpdheads = [
    'http' => [
        'header' => "User-Agent: $ua\r\n",
        'follow_location' => 1,
        'timeout' => 5
    ]
];

$context = stream_context_create($mpdheads);
$res = file_get_contents($mpdUrl, false, $context);

echo $res;
?>
