#!/usr/bin/php
<?php
require __DIR__ . "/../src/bootstrap.php";


$recipient = $argv[1];


function smtp_error ($errorcode, $messge) {
    echo "$errorcode $messge";
    exit (2);
}


$map = unserialize(file_get_contents("/etc/http-forward.ser"));

if ( ! isset ($map[$recipient])) {
    smtp_error("5.5.1", "Recipient address '$recipient' unhandled by httpfwd");
}
$config = $map[$recipient];

$body = file_get_contents("php://stdin");

$req = phore_http_request($config["targetUrl"])
    ->withPostBody($body)
    ->withQueryParams(["recipient" => $recipient]);
if ( ! empty($config["authBasic"])) {
    $req->withBasicAuth($config["authBasic"]);
}

try {
    $req->send();
} catch (\Phore\HttpClient\Ex\PhoreHttpRequestException $e) {
    smtp_error("5.5.0", $e->getMessage());
}
