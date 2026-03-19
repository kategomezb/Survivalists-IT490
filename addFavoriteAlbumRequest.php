<?php

	require_once('includes/path.inc');
	require_once('includes/get_host_info.inc');
	require_once('includes/rabbitMQLib.inc');

	// $username = $_POST['username'];

	$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

	$addFavoriteAlbumRequest = [
		'type' => 'addFavoriteAlbum',
		'session_key' => $_COOKIE['SessionKey'], // FIXED: null username field issue by accessing the stored SessionKey in cookie
		'album' => $_POST['album'],
		'artist' => $_POST['artist'],
	];

	$serverResponse = $client->send_request($addFavoriteAlbumRequest);

	//this is to see if it works
	echo "<pre>";
	print_r($serverResponse);
	echo "</pre>";
?>

<!DOCTYPE html>
<html>
	<body>
        <a href="dashboard.php" class="href">Back to dashboard</a>
	</body>
</html>