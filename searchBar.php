<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

require_once(__DIR__.'/path.inc');
require_once(__DIR__.'/get_host_info.inc');

require_once(__DIR__.'/rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini","testDMZ");

$input = json_decode(file_get_contents("php://input"), true);

$artist = $input['artist'];
$filters = $input['filters'];

$results = [];

foreach($filters as $filter){

    $request = ["type" => "search", "artist" => $artist, "filter" => $filter];

    $response = $client->send_request($request);
 
     // in case the response is an arry i will mergee it with the results.
    if(is_array($response)){
        $results = array_merge($results,$response);
    }

}

// here i can convert the results to json and it will send to the front end
echo json_encode($results);
?>
