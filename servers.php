<?php
header("Content-Type: application/json");
$servers = [
    "west" => [
        "name" => "San Francisco",
        "server" => "//speedtest-west.weboost.com/",
        "dlURL" => "backend/garbage.php",
        "ulURL" => "backend/empty.php",
        "pingURL" => "backend/empty.php",
        "getIpURL" => "backend/getIP.php"
    ],
    "east" => [
        "name" => "New York",
        "server" => "//speedtest-east.weboost.com/",
        "dlURL" => "backend/garbage.php",
        "ulURL" => "backend/empty.php",
        "pingURL" => "backend/empty.php",
        "getIpURL" => "backend/getIP.php"
    ],
    "staging" => [
        "name" => "San Francisco",
        "server" => "//speedtest-staging.weboost.com/",
        "dlURL" => "backend/garbage.php",
        "ulURL" => "backend/empty.php",
        "pingURL" => "backend/empty.php",
        "getIpURL" => "backend/getIP.php"
    ]
];
$primary = $_SERVER["SPEEDTEST_PRIMARY_REGION"] ?? getenv("SPEEDTEST_PRIMARY_REGION") ?: "west";
$ordered = $primary === "staging" ? [$servers["staging"]]
    : ($primary === "east" ? [$servers["east"], $servers["west"]]
        : [$servers["west"], $servers["east"]]);
echo json_encode($ordered);