<?php
require_once('includes/path.inc');
require_once('includes/get_host_info.inc');
require_once('includes/rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

// This is the original
$request = [
    'type' => 'login',
    'username' => $_POST['username'],
    'password' => $_POST['password']
];


$response = $client->send_request($request);

// This will show us if it is generating the session key.
// <pre> </pre> — for testing the session keys
// I used this link as a reference: https://stackoverflow.com/questions/4756842/what-does-php-echo-pre-echo-pre-mean
//echo "<pre>";
//print_r($response);
//echo "</pre>";


if (isset($response['status']) && $response['status'] === 'success') {
    setcookie('SessionKey', $response['session_key'], 0, '/');
    header("Location: dashboard.php");
    exit();
}

// if the login fails i need to display this
echo $response['message'] ?? 'Login failed'; 
//echo "Server response: " . $response['message']; // this is what the teacher had before.
?>
