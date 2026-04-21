<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient('testRabbitMQ.ini', 'testQA');

$loginRequest = [
	'type' => 'login',
	'username' => $_POST['username'],
	'password' => $_POST['password']
];

$response = $client->send_request($loginRequest);

$expiration = time() + 3600;

// COOKIES + SESSIONS REFERENCES:
// 1. https://www.geeksforgeeks.org/computer-networks/session-vs-token-based-authentication/ 
// 2. https://www.php.net/manual/en/function.setcookie.php

if($response['returnCode'] == '0') {
	setcookie("SessionKey", $response['session_key'], $expiration);
	header('Location: userProfile.php');
	//echo 'Login success!';
	exit();
} else {
	header('Location: login.html');
} 

//echo "Server response: " . $response['message'];
?>
