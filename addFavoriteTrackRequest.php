<?php

	require_once('path.inc');
	require_once('get_host_info.inc');
	require_once('rabbitMQLib.inc');

	$client = new rabbitMQClient("testRabbitMQ.ini", "testQA");

	// access stored session key
	// find corresponding User object in reg_users database w/ that session key
	// access that User object and store its username

	$addFavoriteTrackRequest = [
		'type' => 'addFavoriteTrack',
		'session_key' => $_COOKIE['SessionKey'], // FIXED: null username field issue by accessing the stored SessionKey in cookie
		'title' => $_POST['title'],
		'artist' => $_POST['artist']
	];

	$serverResponse = $client->send_request($addFavoriteTrackRequest);

	//this is to see if it works
	echo "<pre>";
	print_r($serverResponse);
	echo "</pre>";
?>

<!DOCTYPE html>
<html>
	<body>
                               <a href="userProfile.php" class="href">Back to Profile</a>
	</body>
</html>
