<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

if (!isset($_POST['username']) || !isset($_POST['password'])) {
    echo "We do not have the username or password.";
    exit();
}

$userInput = $_POST['username'];
$passwordInput = $_POST['password'];

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

//this will show up on the queue
$registration = array(
    'type' => 'registration',
    'username' => $userInput,
    'password' => $passwordInput
);

$serverResponse = $client->send_request($registration);

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
