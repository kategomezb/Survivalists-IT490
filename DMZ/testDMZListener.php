#!/usr/bin/php
<?php

require_once(__DIR__ . '/../path.inc');
require_once(__DIR__ . '/../get_host_info.inc');
require_once(__DIR__ . '/../rabbitMQLib.inc');


$input = readline("Enter an artist, album, or track to search: ");
$inputFilter = trim(readline("Enter one filter (artists, albums or tracks) or leave it blank: "));
$inputFilter = strtolower($inputFilter);

$request = ['type'=> 'searchRequest', 'userInput' => $input]; 

if ($inputFilter === 'artists' || $inputFilter === 'albums' || $inputFilter === 'tracks') {
    $request['userFilters'] = $inputFilter;
}
$client = new rabbitMQClient("testRabbitMQ.ini", "testDMZ");
$response = $client->send_request($request);
print_r($response);
?>
