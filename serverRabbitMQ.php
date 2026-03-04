#!/usr/bin/php
<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');
require '../vendor/autoload.php';

$uri = 'mongodb://100.105.160.23:27017/';
$mongoClient = new MongoDB\Client($uri);
$database = $mongoClient->survivalists_db;

//This is the function for the session key of each login -- keep in mind we will need to link this to the DB as soon as it is done.
//I used the following links as a reference on how to do it: 
//https://stackoverflow.com/questions/48628985/is-it-cryptographically-secure-to-use-bin2hexrandom-bytesstr
//https://www.php.net/manual/en/function.bin2hex.php
function createSessionKey($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

function registration($username, $password) {
    global $database;

    $userCollection =  $database->reg_users;
    
    $existingUser = $userCollection->findOne(array('username' => $username));
    if ($existingUser) {
        return array("returnCode" => '1', "message" => "The username already exists.");
    }
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    $userCollection->insertOne(array(
        "username" => $username,
        "password" => $hashedPassword,
        "keySession" => null,
        "sessionExpiration" => null
    ));

    return array("returnCode" => '0', "message" => "The user was registered.");
}

function login($username, $password) {
	global $database;

	$userCollection = $database->reg_users;

	$query = array('username' => $username, 'password' => $password);
	$result = $userCollection->findOne($query);

	if(!empty($result) && password_verify ($password, $user['password'])) { 
		echo "User was successfully logged in.";

		$session_key = creatSessionKey(); // session_key is the variable holding generated key
		expiration = time() + 3600 // Professor Kehoe said to utilize epoch

		userCollection->updateOne(
			["username" => $username,
        	"keySession" => $session_key, // NOTE: keySession is database's session key variable,  session_key is server's variable
        	"sessionExpiration" => expiration
			]
		);
	} else {
		echo "Credentials were not authenticated.";
	}
}

function requestProcessor($request) {
    if (!isset($request['type'])) {
        return array("returnCode" => '1', "message" => "This is an invalid request type.");

    }

    switch ($request['type']) {
        case "registration":
            return registration($request['username'], $request['password']);
    }
    return array("returnCode" => '1', "message" => "Server received request and processed");
}

$server = new rabbitMQServer("testRabbitMQ.ini", "testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>
