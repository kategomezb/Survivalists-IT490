<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

$client = new rabbitMQClient('testRabbitMQ.ini', 'testServer');

$loginRequest = [
	'type' => 'login',
	'username' => $_POST['username'],
	'password' => $_POST['password']
];

$response = $client->send_request($loginRequest);

$expiration = time() + 3600;

if($response['returnCode'] == '0') {
	setcookie("Session key: ", $response['session_key'], $expiration);
	echo 'Login success!';
	exit();
} else {
	header('Location: login.html');
} 

//echo "Server response: " . $response['message'];
?>
