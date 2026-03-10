<?php
// Show errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('/var/www/includes/path.inc');
require_once('/var/www/includes/get_host_info.inc');
require_once('/var/www/includes/rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

// Check if POST data exists
if (isset($_POST['username']) && isset($_POST['password'])) {

    //i need this to show the message
    $username = $_POST['username'];

    $request = [
        'type' => 'register',
        'username' => $_POST['username'],
        'password' => $_POST['password']
    ];

    $response = $client->send_request($request);
    if (isset($response['status']) && $response['status'] === 'success') {
        // this was on the professor's code but im checking if i can redirect the user to login after registering echo "Server response: " . $response['message']; 
        header("Location: logn.html");
    exit();   
    }
    echo $response['message'] ?? 'Registration failed';
} else {
    echo "Please submit the form with username and password.";
}
?>
