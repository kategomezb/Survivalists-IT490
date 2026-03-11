<?php
// LOGIN SETCOOKIE() LOGIC: setcookie("SessionKey", $response['session_key'], $expiration);

	setcookie("SessionKey", "", time() - 3600);
    header("Location: login.html");
    exit();

?>