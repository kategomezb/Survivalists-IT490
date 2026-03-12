<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../path.inc');
require_once('../get_host_info.inc');
require_once('../rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

// check session
$session_key = $_COOKIE['SessionKey'] ?? null;

if(!$session_key) {
    echo json_encode(["status" => "error", "message" => "You must be logged in to get recommendations."]);
    exit();    
}

$request = [
    'type' => 'recommendation',
    'session_key' => $session_key
];

$response = $client->send_request($request);

if(isset($response['status']) && $response['status'] === 'success') {
    // store recommendations in session cookie then redirect
    setcookie('Recommendations', json_encode($response['recommendations']), 0, '/');
    header("Location: recommendations.php");
    exit();
}

echo $response['message'] ?? 'Could not fetch recommendations.';
?>