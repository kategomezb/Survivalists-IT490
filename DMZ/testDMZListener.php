#!/usr/bin/php
<?php

require_once(__DIR__ . '/../path.inc');
require_once(__DIR__ . '/../get_host_info.inc');
require_once(__DIR__ . '/../rabbitMQLib.inc');


$input = readline("Enter an artist, album, or track to search: ");
$request = ['type'=> 'searchRequest', 'userInput' => $input];

$client = new rabbitMQClient("testRabbitMQ.ini", "testDMZ");
$response = $client->send_request($request);
print_r($response);
?>
