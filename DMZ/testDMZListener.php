#!/usr/bin/php
<?php

require_once(__DIR__ . '/../includes/path.inc');
require_once(__DIR__ . '/../includes/get_host_info.inc');
require_once(__DIR__ . '/../includes/rabbitMQLib.inc');

echo "TIDAL SEARCH :D" . PHP_EOL;


$artist = readline("Enter an artist, album, or track to search: ");
$type = strtolower(readline("Select one (artists / albums / tracks) "));

$request = ["type"=> 'search', "artist" => $artist, "filter" => $type]; 

$client = new rabbitMQClient(__DIR__ . "/../testRabbitMQ.ini", "testDMZ");
$response = $client->send_request($request);
print_r($response);

?>