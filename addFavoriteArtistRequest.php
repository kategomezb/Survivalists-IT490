<?php
	
	require_once('path.inc');
	require_once('get_host_info.inc');
	require_once('rabbitMQLib.inc');

	// $username = $_POST['username'];

	$client = new rabbitMQClient("testRabbitMQ.ini", "testServer");

	$addFavoriteArtistRequest = [
		'type' => 'addFavoriteArtist',
		'session_key' => $_COOKIE['SessionKey'], // FIXED: null username field issue by accessing the stored SessionKey in cookie
		'artist' => $_POST['artist']
	];

	$serverResponse = $client->send_request($addFavoriteArtistRequest);

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
