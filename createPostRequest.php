<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if (!isset($_POST['username']) || !isset($_POST['password'])) {
    echo "We do not have the username or password.";
    exit();
}

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

//this will show up on the queue
$createPostRequest = array(
    'type' => 'createPost',
	'session_key' => $_COOKIE['SessionKey'], // FIXED: null username field issue by accessing the stored SessionKey in cookie
    'media' => $media,
    'content' => $content,
    'postedAt' => $postedAt
);

$serverResponse = $client->send_request($createPostRequest); // FIXED: createPost function was redirecting to registration instead of createPostRequest

//this is to see if it works
echo "<pre>";
print_r($serverResponse);
echo "</pre>";
?>
<!DOCTYPE html>
<html>
<body>
<a href="login.html">Go back to the login page.</a>
</body>
</html>
