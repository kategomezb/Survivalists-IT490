<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors',1);
error_reporting(E_ALL);

require_once('includes/path.inc');
require_once('includes/get_host_info.inc');
require_once('includes/rabbitMQLib.inc');

$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

if(isset($_POST['item_id']) && isset($_POST['item_type']) && isset($_POST['rating'])) {

    // get session key from cookie to identify the user
    $session_key = $_COOKIE['SessionKey'] ?? null;

    if(!$session_key) {
        echo "You must be logged in to leave a review.";
        exit();
    }

    $request = [
        'type' => 'review',
        'session_key' => $session_key,
        'item_id' => $_POST['item_id'], // tidal ID of song, album, a
        'item_type' => $_POST['item_type'], // song, album, artist
        'rating' => $_POST['rating'], // 1-5 rating
        'review_text' => $_POST['review_text'] ?? ''
    ];

    $response = $client->send_request($request);

    if(isset($response['status']) && $response['status'] === 'success') {
        header("Location: dashboard.php");
        exit();
    }

    echo $response['message'] ?? 'Review submission failed';
} else {
    echo "Please submit the form with all required fields.";
}
?>