<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');


// This endpoint is now called via fetch() from mediaSearch.php with a JSON body,
// so we read raw input instead of $_POST.
// REF: https://www.php.net/manual/en/wrappers.php.php#wrappers.php.input
$rawInput = file_get_contents("php://input");
$input    = json_decode($rawInput, true);


// Always respond with JSON so the front-end fetch handler can read returnCode
header('Content-Type: application/json');


// Guard: session cookie must be present
// REF: https://www.geeksforgeeks.org/php/php-cookies/
if (!isset($_COOKIE['SessionKey'])) {
    echo json_encode(["returnCode" => "1", "message" => "Not authenticated."]);
    exit();
}


// Guard: required fields must be present in the decoded body
if (!isset($input['media']) || !isset($input['content'])) {
    echo json_encode(["returnCode" => "1", "message" => "Missing media or content field."]);
    exit();
}


$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");


// media arrives as an array (decoded from JSON object); re-encode it as a JSON
// string so it is stored as a single text field in MongoDB, consistent with
// how the rest of the codebase treats the media column.
// REF: https://www.php.net/manual/en/function.json-encode.php
$mediaJson = json_encode($input['media']);


$createPostRequest = [
    'type'      => 'createPost',
    'session_key' => $_COOKIE['SessionKey'],
    'media'     => $mediaJson,              // full item JSON blob as a string
    'content'   => $input['content'],
    'postedAt'  => $input['postedAt'] ?? time()
];


$serverResponse = $client->send_request($createPostRequest);


// Return whatever the server sent back so the front-end can read returnCode
echo json_encode($serverResponse);
?>

